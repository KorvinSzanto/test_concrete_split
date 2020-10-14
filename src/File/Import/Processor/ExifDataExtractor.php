<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Imagine\Image\Metadata\ExifMetadataReader;

class ExifDataExtractor implements PostProcessorInterface
{
    /**
     * @var bool
     */
    protected $populateFileNameAttributes;

    /**
     * @var bool
     */
    protected $populateDescriptionAttributes;

    /**
     * @var bool
     */
    protected $populateKeywordAttributes;

    /**
     * @var bool
     */
    protected $populateAdditionalAttributes;

    /**
     * @var \Concrete\Core\Attribute\Category\CategoryService
     */
    protected $categoryService;

    /**
     * @var \Concrete\Core\Attribute\SetFactory
     */
    protected $setFactory;

    public function __construct(CategoryService $categoryService, SetFactory $setFactory)
    {
        $this->categoryService = $categoryService;
        $this->setFactory = $setFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::getPostProcessPriority()
     */
    public function getPostProcessPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        $this->populateFileNameAttributes = $config->get('concrete.file_manager.images.use_exif_data_for_file_name_attribute');
        $this->populateDescriptionAttributes = $config->get('concrete.file_manager.images.use_exif_data_for_description_attribute');
        $this->populateKeywordAttributes = $config->get('concrete.file_manager.images.use_exif_data_for_keyword_attribute');
        $this->populateAdditionalAttributes = $config->get('concrete.file_manager.images.use_exif_data_for_additional_attributes');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::shouldPostProcess()
     */
    public function shouldPostProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion)
    {
        return
            (
                $this->populateFileNameAttributes ||
                $this->populateDescriptionAttributes ||
                $this->populateKeywordAttributes ||
                $this->populateAdditionalAttributes
            ) &&
            ExifMetadataReader::isSupported() &&
            $file->getFileType()->getName() === 'JPEG'
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::postProcess()
     */
    public function postProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion)
    {
        $categoryEntity = $this->categoryService->getByHandle('file');
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();

        $keywords = [];

        foreach ($this->listExitMetadata($importedVersion) as $label => $value) {
            // create a handle
            $handle = 'exif_' . str_replace(' ', '_', strtolower($label));

            if (preg_match('/[a-zA-Z]/i', $value)) {
                // collect the keyword
                $keywords[] = $value;
            }

            // process the current tag
            switch ($handle) {
                /*
                 * Use the following tags for populating the title:
                 * - Exif.Image.ReelName
                 * - Exif.Image.OriginalRawFileName
                 *
                 * @see: https://www.exiv2.org/tags.html
                 */

                case 'exif_image_original_raw_file_name':
                case 'exif_image_reel_name':
                    if ($this->populateFileNameAttributes) {
                        $importedVersion->updateTitle($value);
                    }

                    break;
                /*
                 * Use the following tags for populating the description:
                 * - Exif.Image.ImageDescription
                 * - Exif.Photo.UserComment
                 *
                 * @see: https://www.exiv2.org/tags.html
                 */

                case 'exif_image_image_description':
                case 'exit_photo_user_comment':
                    if ($this->populateDescriptionAttributes) {
                        $importedVersion->updateDescription($value);
                    }

                    break;
                // All other tags are added to additional file attributes

                default:
                    if ($this->populateAdditionalAttributes) {
                        $key = $category->getAttributeKeyByHandle($handle);

                        if (!is_object($key)) {
                            // create attribute key
                            $key = new FileKey();
                            $key->setAttributeKeyHandle($handle);
                            $key->setAttributeKeyName($label);
                            $key->setIsAttributeKeySearchable(false);
                            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                            $key = $category->add('text', $key, null);

                            $set = $this->setFactory->getByHandle('exit_tags');

                            if (!$set instanceof Set) {
                                // create set
                                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                                $set = $setManager->addSet('exit_tags', t('EXIF Tags'));
                            }

                            // add attribute key to set
                            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                            $setManager->addKey($set, $key);
                        }

                        // add attribute to file version
                        $importedVersion->setAttribute($key, $value);
                    }

                    break;
            }
        }

        if (count($keywords) > 0) {
            $importedVersion->updateTags(str_replace(' ', ', ', implode(', ', $keywords)));
        }
    }

    /**
     * @return \Generator
     */
    protected function listExitMetadata(Version $importedVersion)
    {
        $metadata = $this->getMetadataBag($importedVersion);
        // transform camelcase to normal words
        $re = '/(?#! splitCamelCase Rev:20140412)
            # Split camelCase "words". Two global alternatives. Either g1of2:
              (?<=[a-z])      # Position is after a lowercase,
              (?=[A-Z])       # and before an uppercase letter.
            | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
              (?=[A-Z][a-z])  # and before upper-then-lower case.
            /x';
        foreach ($metadata->toArray() as $key => $value) {
            if (substr($key, 0, 5) === 'exif.' && (string) $value !== '') {
                $matches = preg_split($re, substr($key, 5));
                $label = implode(' ', $matches);
                yield $label => $value;
            }
        }
    }

    /**
     * @return \Imagine\Image\Metadata\MetadataBag
     */
    protected function getMetadataBag(Version $importedVersion)
    {
        if ($importedVersion->hasImagineImage()) {
            return $importedVersion->getImagineImage()->metadata();
        }
        $fr = $importedVersion->getFileResource();
        $metadataReader = new ExifMetadataReader();

        return $metadataReader->readData($fr->read());
    }
}
