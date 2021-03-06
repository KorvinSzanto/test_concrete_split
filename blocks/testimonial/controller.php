<?php
namespace Concrete\Block\Testimonial;

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\BlockController;
use Core;

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 450;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btInterfaceHeight = 560;
    protected $btExportFileColumns = array('fID', 'awardImageID');
    protected $btTable = 'btTestimonial';

    public function getBlockTypeDescription()
    {
        return t("Displays a quote or paragraph next to biographical information and a person's picture.");
    }

    public function getBlockTypeName()
    {
        return t("Testimonial");
    }

    public function getSearchableContent()
    {
        return $this->name . "\n" . $this->position . "\n" . $this->company . "\n" . $this->paragraph;
    }

    public function view()
    {
        $image = false;
        if ($this->fID) {
            $f = \File::getByID($this->fID);
            if (is_object($f)) {
                $image = Core::make('html/image', array('f' => $f))->getTag();
                $image->alt($this->name);
                $this->set('image', $image);
            }
        }
        $awardImage = false;
        if ($this->awardImageID) {
            $f = \File::getByID($this->awardImageID);
            if (is_object($f)) {
                $awardImage = Core::make('html/image', array('f' => $f))->getTag();
                $this->set('awardImage', $awardImage);
            }
        }
    }

    public function save($args)
    {
        $args['fID'] = (isset($args['fID']) && $args['fID'] != '') ? $args['fID'] : 0;
        $args['awardImageID'] = (isset($args['awardImageID']) && $args['awardImageID'] != '') ? $args['awardImageID'] : 0;
        parent::save($args);
    }
}
