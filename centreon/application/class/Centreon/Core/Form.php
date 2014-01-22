<?php
/*
 * Copyright 2005-2014 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

namespace Centreon\Core;

/**
 * @author Lionel Assepo <lassepo@merethis.com>
 * @package Centreon
 * @subpackage Core
 */
class Form
{
    /**
     *
     * @var \HTML_QuickForm
     */
    private $formProcessor;
    
    /**
     *
     * @var HTML_QuickForm_Renderer_ArraySmarty
     */
    private $formRenderer;
    
    /**
     *
     * @var type 
     */
    private $options;
    
    /**
     *
     * @var type 
     */
    private $defaultValue;
    
    /**
     *
     * @var \Centreon\Core\Template
     */
    private $tpl;
    
    /**
     *
     * @var type
     */
    private $template;

    /**
     *
     * @var \Centreon\Core\Di 
     */
    private $di;
    
    /**
     * The style for quickform elements
     * 
     * @var array
     */
    private $style;
    
    /**
     * The separator for quickform elements
     *
     * @var string
     */
    private $basicSeparator;
    
    /**
     * Javascript rules register
     *
     * @var array
     */
    private $jsRulesRegister = array(
        'required',
        'min',
        'max',
        'range',
        'email',
        'url',
        'date',
        'dateISO',
        'number',
        'digits',
        'creditcard',
        'equalTo',
        'phoneUS'
    );
    
    /**
     * Javascript rules 
     *
     * @var array
     */
    private $jsRules;
    
    /**
     * Constructor
     *
     * @param string $name The name of form
     * @param array $options The options
     */
    public function __construct($name, $options = null)
    {
        $this->formProcessor = new \HTML_QuickForm($name, 'post');
        $this->options = $options;
        $this->init();
        $this->defaultValue = array();
        $this->di = \Centreon\Core\Di::getDefault();
        $this->tpl = $this->di->get('template');
    }
    
    /**
     * 
     * @return \HTML_QuickForm
     */
    public function toSmarty()
    {
        $this->formRenderer = new \HTML_QuickForm_Renderer_ArraySmarty($this->tpl, true);
        $this->formRenderer->setRequiredTemplate('{label}<font color="red" size="1">*</font>');
        $this->formRenderer->setErrorTemplate('<font color="red">{error}</font><br />{html}');
        $this->formProcessor->accept($this->formRenderer);
        return $this->formatForSmarty();
    }
    
    /**
     * 
     * @param array $smartyArray
     */
    private function formatForSmarty()
    {
        
        $smartyArray = $this->formRenderer->toArray();
        $finalArray = array (
            'frozen' => $smartyArray['frozen'],
            'javascript' => $smartyArray['javascript'],
            'attributes' => $smartyArray['attributes'],
            'requirednote' => $smartyArray['requirednote'],
            'errors' => $smartyArray['errors'],
            'hidden' => $smartyArray['hidden']
        );
        
        if (isset($smartyArray['elements'])) {
            foreach ($smartyArray['elements'] as $element) {
                $finalArray[$element['name']] = array();
                foreach ($element as $key => $value) {
                    $finalArray[$element['name']][$key] = $value;
                }
                $this->renderAsHtml($finalArray[$element['name']]);
            }
        }
        
        return $finalArray;
    }
    
    public function renderAsHtml(&$element)
    {
        
        if (!isset($element['label']) || (isset($element['label']) && empty($element['label']))) {
            $element['label'] = $element['name'];
        }
        
        if (!isset($element['placeholder']) || (isset($element['placeholder']) && empty($element['placeholder']))) {
            $element['placeholder'] = $element['name'];
        }
        
        if (!isset($element['id']) || (isset($element['id']) && empty($element['id']))) {
            $element['id'] = $element['name'];
        }
        
        switch ($element['type']) {
            case 'text':
            case 'button':
            case 'password':
            default:
                $element['input'] = '<input '.
                            'id="'.$element['id'].'" '.
                            'type="'.$element['type'].'" '.
                            'name="'.$element['name'].'" '.
                            'class="form-controler" '.
                            'placeholder="'.$element['placeholder'].'" '.
                            '/>';
                break;
        }
        
        $element['label'] = '<label class="sr-only" for="'.$element['id'].'">'.$element['label'].'</label>';
        $element['html'] = '<div class="form-group">'.
                        $element['label'].
                        $element['input'].
                        '</div>';
    }

    /**
     * 
     * @param type $name
     * @param type $fieldType
     * @param type $additionalParameters
     */
    public function add($name, $fieldType = 'text', $label = "", $additionalParameters = array())
    {
        if (empty($label)) {
            $label = $name;
        }
        
        switch (strtolower($fieldType)) {
            case 'button':
                $this->checkParameters($additionalParameters, array('params' => array()));
                $this->addButton($name, $label, $additionalParameters['params']);
                break;
            case 'checkbox':
                $this->checkParameters($additionalParameters, array('params' => array()));
                $this->formProcessor->addElement($name, $label, $additionalParameters['params']);
                break;
            case 'hidden':
                $this->checkParameters($additionalParameters, array('value' => ''));
                $this->formProcessor->addElement('hidden', $name, $additionalParameters['value']);
                break;
            case 'radio':
                $this->checkParameters(
                    $additionalParameters,
                    array(
                        'elements' => array(),
                        'defaultValue' => null
                    )
                );
                $this->addElement(
                    $name,
                    $label,
                    $additionalParameters['elements'],
                    $additionalParameters['defaultValue']
                );
                break;
            case 'reset':
                $this->checkParameters($additionalParameters, array('params' => array()));
                $this->addReset($name, $label, $additionalParameters['params']);
                break;
            case 'select':
                $this->checkParameters(
                    $additionalParameters,
                    array(
                        'multiple' => false,
                        'data' => array(),
                        'style' => null
                    )
                );
                if ($additionalParameters['multiple']) {
                    $this->addMultiSelect(
                        $name,
                        $label,
                        $additionalParameters['data']
                    );
                } else {
                    $this->addSelect(
                        $name,
                        $label,
                        $additionalParameters['data'],
                        $additionalParameters['style']
                    );
                }
                break;
            case 'submit':
                $this->checkParameters($additionalParameters, array('params' => array()));
                $this->addSubmit($name, $label, $additionalParameters['params']);
                break;
            case 'submitbar':
                $this->checkParameters($additionalParameters, array('cancel' => true));
                $this->addSubmitBar($name = 'submitbar', $additionalParameters['cancel']);
                break;
            case 'textarea':
                $this->addTextarea($name, $label);
                break;
            default:
            case 'text':
                $this->checkParameters(
                    $additionalParameters,
                    array(
                        'style' => null,
                        'placeholder' => null,
                        'help' => null
                    )
                );
                $this->addText(
                    $name,
                    $label,
                    $additionalParameters['style'],
                    $additionalParameters['placeholder'],
                    $additionalParameters['help']
                );
                break;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param type $given
     * @param type $mandatory
     */
    private function checkParameters(&$given, $mandatory)
    {
        foreach ($mandatory as $field => $value) {
            if (!isset($given[$field])) {
                $given[$field] = $value;
            }
        }
    }
    
    /**
     * Add a input text element
     *
     * @param string $name The name and the id of element
     * @param string $label The label of element
     * @param string|null $style The input style (prefix by input-)
     *                           if null the style is medium
     * @return \HTML_QuickForm_Element_InputText
     */
    public function addText($name, $label, $style = null, $placeholder = null, $help = null)
    {
        if (is_null($style)) {
            $style = "medium";
        }
        $param = array();
        if (!is_null($placeholder)) {
            $param['placeholder'] = $placeholder;
        }
        if (!is_null($help)) {
            $param['_help'] = $help;
        }
        $elem = $this->formProcessor
            ->addElement('text', $name, $label ,$param)
            ->updateAttributes(
                array(
                    'id'=>$name,
                    'class' => "input-".$style,
                    'label' => $label
                )
            );
        return $elem;
    }
    
    /**
     * Add a radio to the form
     *
     * @param string $name The name and the id of element
     * @param string $label The label of element
     * @param array $params The list of options in option group
     * @param string $defaultValue The default value for radio
     * @return \HTML_QuickForm_Container_Group
     * @todo Default
     */
    public function addElementRadio($name, $label, $elements, $defaultValue = null)
    {
        $elem = $this->formProcessor->addInputList($name)
            ->updateAttributes(
                array(
                    'id'=>$name,
                    'label'=> $label
                )
            );
        foreach ($elements as $key => $value) {
            $elem->addRadio(
                $name,
                array('value' => $key)
            )
                ->setContent($value);
        }
        $this->defaultValue[$name] = $defaultValue;
        return $elem;
    }
    
    /**
     * Add a select
     *
     * @param string $name The name and the id of element
     * @param string $label The label of element
     * @param array $data The list for options
     * @param string|null $style The input style (prefix by input-)
     *                           if null the style is medium
     * @return \HTML_QuickForm_Element_Select
     */
    public function addSelect($name, $label, $data, $style = null)
    {
        $elem = $this->formProcessor
                        ->addElement('select', $name, array('type' => 'select-one'))
                        ->updateAttributes(array('id'=>$name, 'label'=>$label))
                        ->loadOptions($data);
        if (!is_null($style)) {
            $elem->addClass($style);
        }
        return $elem;
    }
    
    /**
     * Add a multiselect
     *
     * @param string $name The name and the id of element
     * @param string $label The label of element
     * @param array $data The list for options
     * @param string|null $style The input style (prefix by input-)
     *                           if null the style is medium
     * @return \HTML_QuickForm_Element_Select
     */
    public function addMultiSelect($name, $label, $data)
    {
        $this->tpl->addCss('jquery-chosen.css');
        $this->tpl->addJs('jquery/chosen/chosen.jquery.min.js');
        $this->tpl->addJs('centreon/formMultiSelect.js');
        $elem = $this->formProcessor
                    ->addElement('select', $name, array('multiple' => 'multiple'))
                    ->updateAttributes(
                        array(
                            'id'=>$name,
                            'class'=>'chzn-select',
                            'label'=>$label
                        )
                    )
                    ->loadOptions($data);
        return $elem;
    }
    
    /**
     * Add a checkbox to the form
     *
     * @param string $name The name and the id of element
     * @param string $label The label of element
     * @param array $params The list of options in option group
     * @return \HTML_QuickForm_Container_Group
     */
    public function addCheckBox($name, $label, $params = array())
    {
        $this->formProcessor->addElement("checkbox", $name, $label)
                            ->updateAttributes(array('id'=>$name));
        
        if (!is_null($params) && count($params)) {
            
            $cbg = array();
            foreach ($params as $key => $value) {
                $cbg[] = $this->formProcessor->createElement("checkbox", $name, $label)
                            ->updateAttributes(array('id'=>$name))->addCheckbox($name)
                    ->setValue($key)
                    ->setContent($value);
            }
        } else {
            $cbg = $this->formProcessor->addInputList('ctn_'.$name)
                    ->updateAttributes(array('id'=>'ctn_'.$name, 'label'=>$label));
            $cbg->addCheckbox($name)
                    ->updateAttributes(array('id'=>$name))
                    ->setValue($name);
        }
        return $cbg;
    }

    /**
     * Add a textarea to the form
     *
     * @param string $name The name and the id of element
     * @param string $label The label of element
     * @return \HTML_QuickForm_Element_Textarea
     */
    public function addTextarea($name, $label)
    {
        $elem = $this->formProcessor
                    ->addElement('textarea', $name, $this->template['textarea'])
                    ->updateAttributes(array('id'=>$name, 'label'=>$label));
        return $elem;
    }
    
    /**
     * Add a hidden element
     *
     * @param string $name The name of hidden element
     * @param string $value The value of hidden element
     * @return \HTML_QuickForm_Element_InputHidden
     */
    public function addElementHidden($name, $value)
    {
        $elem = $this->formProcessor
                    ->addElement('hidden', $name)
                    ->updateAttributes(array('id'=>$name))
                    ->setValue($value);
        return $elem;
    }
    
    /**
     * Add a button to the form
     *
     * @param string $name The name of button
     * @param string $value The value of button
     * @param array $param Additionnal param
     * @return \HTML_QuickForm_Element_Button
     */
    public function addButton($name, $label, $params = array())
    {
        $params['id'] = $name;
        $this->formProcessor->addElement('button', $name, $label, $params);
    }

    /**
     * Add a submit to the form
     *
     * @param string $name The name of submit
     * @param string $value The value of submit
     * @param array $param Additionnal param
     * @return \HTML_QuickForm_Element_InputSubmit
     */
    public function addSubmit($name, $label, $params = array())
    {
        $this->formProcessor->addElement('submit', $name, $label, $params)
                ->updateAttributes(array('id'=>$name, 'class'=>'btn-primary'));
    }

    /**
     * Add a reset to the form
     *
     * @param string $name The name of reset
     * @param string $value The value of reset
     * @param array $param Additionnal param
     * @return \HTML_QuickForm_Element_InputReset
     */
    public function addReset($name, $label, $params = array())
    {
        $elem = $this->formProcessor
                        ->addElement('reset', $name, $label, $params)
                        ->updateAttributes(array('id'=>$name));
        return $elem;
    }
    
    /**
     * Add the submit bar to a form
     * 
     * @param string $name The name of bar
     * @param boolean $cancel If include the cancel button
     * @return Centreon_SubmitBar
     */
    public function addSubmitBar($name = 'submitbar', $cancel = true)
    {
        $submitbar = $this->formProcessor
                            ->addElement('submitbar', $name)
                            ->updateAttributes(array('id'=>$name));
        
        $submitbar
            ->addElement('submit', 'submit')
            ->updateAttributes(array('id'=>'submit', 'label'=>_('Save changes'), 'class'=>'btn-primary'));
        
        if ($cancel) {
            $submitbar
                ->addElement('reset', 'reset')
                ->updateAttributes(array('id'=>'reset', 'label'=>_('Cancel')));
        }
        
        return $submitbar;
    }
    
    /**
     * Add clonable element
     * 
     * @param string $type
     * @param string $name
     * @param string $label
     * @param array $options
     * @param string $style
     * @return \HTML_QuickForm_Element
     */
    public function addClonableElement($type, $name, $label, $options = array(), $style = null)
    {
        switch (strtolower($type)) {
            case 'text':
                $elem = $this->addText($name, $label, $style);
                break;
            case 'select':
                $elem = $this->addSelect($name, $label, $options, $style);
                break;
            case 'checkbox':
                $elem = $this->addCheckBox($name, $label, $options);
                break;
            default:
                throw new Centreon_Exception_Core('Element type cannot be cloned');
        }
        
        $elem
            ->updateAttributes(array('id'=>$name."_#index#"))
            ->setName($name."[#index#]");
        
        return $elem;
    }
    
    /**
     * Add a tab into the form
     *
     * @param string $id The tab id
     * @param string $label The tab label
     * @return QuickForm_Container_Tab
     */
    public function addTab($id, $label)
    {
        return $this->formProcessor
                    ->addElement('tabs', $label)
                    ->updateAttributes(array('id'=>$id, 'label'=>$label));
    }

    /**
     * Add a fieldset into the form
     *
     * @param string $label The legend
     * @return \HTML_QuickForm_Container_Fieldset
     */
    public function addFieldSet($label)
    {
        return $this->formProcessor
                    ->addElement('fieldset', $label)
                    ->setLabel($label);
    }

    /**
     * Return the array for smarty
     * 
     * @return type
     */
    public function display()
    {
        $this->setDefaults($this->defaultValue);
        $renderer = \HTML_QuickForm_Renderer::factory('centreon');
        $this->formProcessor->render($renderer);

        $this->formProcessor->addRecursiveFilter("trim");
        
        return $this->rulesToArray($renderer->toArray());
    }

    /**
     * Add help by block
     *
     * The array help format :
     *
     *     array(
     *       'elementname' => 'help string'
     *     )
     *
     * @param array $helps The helps
     */
    public function addHelps($helps)
    {
        foreach ($helps as $element => $help) {
            $formEl = $this->formProcessor->getElementsByName($element);
            if (count($formEl) > 0) {
                $formEl[0]->setAttribute('_help', $help);
            }
        }
    }

    /**
     * Register a rule
     * 
     * @param string $name The rule name
     * @param string|array $function The callback
     */
    public function registerRule($name, $function)
    {
        \HTML_QuickForm_Factory::registerRule(
            $name,
            '\HTML_QuickForm_Rule_Callback',
            'HTML/QuickForm/Rule/Callback.php',
            $function
        );
    }

    /**
     * Register a javascript rule
     *
     * @param string $name The rule name
     * @param string $file The javascript file who add the rule
     */
    public function registerJsRule($name, $file)
    {
        if (!in_array($name, $this->jsRulesRegister)) {
            $this->tpl->addJavascript($file);
            $this->jsRulesRegister[] = $name;
        }
    }


    /**
     * Add rule for form
     *
     * @param string $ruleName The rule name
     * @param string $field The field name
     * @param string $msg The message
     * @param string|null $jsExt Extended information for javascript
     */
    public function addRule($ruleName, $field, $msg, $jsExt = null)
    {
        /* If Quickform rule exists */
        if (\HTML_QuickForm_Factory::isRuleRegistered($ruleName)) {
            $elements = $this->formProcessor->getElementsByName($field);
            foreach ($elements as $element) {
                $this->formProcessor->addRule($ruleName, $msg);
            }
        }
        /* If javascript rule exists */
        if (in_array($ruleName, $this->jsRulesRegister)) {
            if (is_null($jsExt)) {
                $jsExt = 'true';
            }
            $this->jsRules[$field][] = array(
                'rule' => $ruleName,
                'message' => $msg,
                'info' => $jsExt
            );
            /* Add javascript for initialize the form rules */
            $this->tpl->addJavascript('jquery/validate/jquery.validate.min.js');
            $this->tpl->addJavascript('centreon/formRules.js');
        }
    }

    /**************************************/

    public function addMassiveChangeUpdateOption($name, $defaultValue, $o)
    {
        if ($o == "mc") {
            $this->formProcessor->addElementRadio(
                $name,
                _("Update mode"),
                array(
                    0 => _("Incremental"),
                    1 => _("Replacement")
                ),
                $defaultValue
            );
        }
    }

    public function applyFilter($field, $function)
    {
        //$this->formProcessor->applyFilter($field, array($this, $function));
        //$this->formProcessor->addFilter($field, array($this, $function));
    }
     
    public function setDefaults($values)
    {
        //$this->formProcessor->addDataSource(new \HTML_QuickForm_DataSource_Array($values));
        //$this->formProcessor->addDataSource(new \HTML_QuickForm_DataSource_Array($values));
        
    }

    /**
     * Enable or Disable freeze status
     * @params boolean
     *
     */
    public function freeze($bool = 1)
    {
        $this->formProcessor->toggleFrozen($bool);
    }
    
    /**
     * Returns the element's value, possibly with filters applied
     *
     */
    public function getValue()
    {
        return $this->formProcessor->getValue();
    }
    
    /**
     * 
     * @param type $elem
     * @return string
     */
    public function getSubmitValue($elem = null)
    {
        if (!isset($elem)) {
            return ""; //$this->formProcessor->getSubmitValue();
        } else {
            return "" ; //$this->formProcessor->getSubmitValue($elem);
        }
    }

    /**
     * 
     * @param type $elem
     * @return type
     */
    public function getSubmitValues($elem = null)
    {
        if (!isset($elem)) {
            return $this->formProcessor->getSubmitValues();
        } else {
            return $this->formProcessor->getSubmitValues($elem);
        }
    }
    
    /**
     * 
     */
    public function isSubmitted()
    {
        $this->formProcessor->isSubmitted();
    }

    /**
     * 
     * @param type $elem
     * @return type
     */
    public function getElement($elem)
    {
        return $this->formProcessor->getElement($elem);
    }
    
    /**
     * 
     * @return type
     */
    public function validate()
    {
        return $this->formProcessor->validate();
    }

    /**
     * 
     * @param type $field Specific rules
     * @return type
     */
    private function removeSpaces($field)
    {
        $ret = $this->formProcessor->getSubmitValues();
        return (str_replace(" ", "_", $ret[$field]));
    }

    /****************************************************/

    /**
     * Add javascript rules in end of form array
     *
     * @param array $array The quickform to array
     * @return array
     */
    private function rulesToArray($array)
    {
        $array['rules'] = $this->jsRules;
        return $array;
    }

    /**
     * Initialiaze the form templating style
     */
    private function init()
    {
        $this->template = array();
        $this->template['textarea'] = array('rows' => '6', 'cols' => '120');
        
        $this->style = array();
    
        $this->basicSeparator = '&nbsp;';
        
        /*\HTML_QuickForm_Factory::registerElement(
            'inputlist',
            'Centreon_InputList'
        );
        \HTML_QuickForm_Factory::registerElement(
            'tabs',
            'QuickForm_Container_Tab'
        );
        \HTML_QuickForm_Factory::registerElement(
            'submitbar',
            'Centreon_SubmitBar'
        );

        \HTML_QuickForm_Renderer::register(
            'centreon',
            'QuickForm_Renderer_Centreon_Horizontal'
        );*/
    }
}
