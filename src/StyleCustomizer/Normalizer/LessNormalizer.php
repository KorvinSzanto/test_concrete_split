<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

class LessNormalizer implements NormalizerInterface
{

    /**
     * @var \Less_Parser
     */
    protected $parser;

    public function __construct(\Less_Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $variableName
     * @return string
     */
    protected function normalizeVariableName(string $variableName)
    {
        return str_replace('@', '', $variableName);
    }

    public function createVariableCollectionFromFile(string $variablesFilePath): NormalizedVariableCollection
    {
        $collection = new NormalizedVariableCollection();
        $parser = $this->parser->parseFile($variablesFilePath, '', true);
        $rules = $parser->rules;

        // load required preset variables.
        foreach ($rules as $rule) {
            if ($rule instanceof \Less_Tree_Rule) {
                $variableName = $this->normalizeVariableName($rule->name);
                if ($rule->value instanceof \Less_Tree_Value) {
                    if (is_array($rule->value->value)) {
                        $valueValue = $rule->value->value[0];
                        if ($valueValue instanceof \Less_Tree_Expression) {
                            $expressionValue = $valueValue->value[0];
                            if ($expressionValue instanceof \Less_Tree_Quoted) {
                                $collection->add(new Variable($variableName, $expressionValue->value));
                            } else if ($expressionValue instanceof \Less_Tree_Color) {
                                $rgbaValue = null;
                                $colorValue = $expressionValue->rgb;
                                if (isset($colorValue[3])) {
                                    $rgbaValue = $colorValue[3];
                                }
                                $collection->add(new ColorVariable($variableName, $colorValue[0], $colorValue[1], $colorValue[2], $rgbaValue));
                            } else if ($expressionValue instanceof \Less_Tree_Dimension) {
                                $value = $expressionValue->value;
                                $expressionUnit = $expressionValue->unit;
                                $unit = null;
                                if ($expressionUnit instanceof \Less_Tree_Unit) {
                                    if (isset($expressionUnit->numerator[0])) {
                                        $unit = $expressionUnit->numerator[0];
                                    }
                                }
                                $collection->add(
                                    new NumberVariable($variableName, $value, $unit)
                                );
                            } else if ($expressionValue instanceof \Less_Tree_Keyword) {
                                $collection->add(new Variable($variableName, $expressionValue->value));
                            }
                        }
                    }
                }
            }
        }
        return $collection;
    }


}
