<?php 
namespace Utils;
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Forms {
    public static function generateForm($config) {
       //print_r($config);
       //echo "Début de la génération du formulaire<br>";

        $formHtml = '<form id="' . $config['formId'] .'" action="' . $config['trait'] . '"';
        if (isset($config['enctype'])) {
            $formHtml .= ' method = "post" enctype="' . $config['enctype'] . '"';
        }
        else { $formHtml .= " method='" . $config['method'] . "'"; }
        $formHtml .= '>';
        //echo "HTML enn cours1 : $formHtml<br>";

        foreach ($config['fields'] as $field) {
            $class = isset($field['class']) ? ' ' . $field['class'] : '';
            $data = isset($field['data']) ? ' ' . $field['data'] : '';

            //  TEXTAREA
            if (isset($field['type']) && $field['type'] == 'textarea') {
                $formHtml .=  '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
                $formHtml .=  '<textarea id="' . $field['id'] . '" name="' . $field['name'] . '" ' . (isset($field['disabled']) ? $field['disabled'] : '') . ' ';
                if (isset($field['class'])) {
                    $formHtml .=  ' class="' . $field['class'] . '"';
                }
                if (isset($field['data'])) {
                    foreach ($field['data'] as $key => $value) {
                        $formHtml .=  ' data-' . $key . '="' . $value . '"';
                    }
                }
                $formHtml .=  '>' . $field['msg'] . '</textarea>';
            } 
            if (isset($field['type'])) {
                //      SUBMIT
                if ($field['type'] == 'submit') { //rint_r($field);
                //  if (isset($config['data'])) { //$data = $field['data']; } else { $data='';}
                    $formHtml .= '<button type="submit" ' . (isset($field['disabled']) ? $field['disabled'] : '') . '  '.$data.' class="w-50 btn btn-lg btn-primary ' . $class . '" id=" ' .(isset($field['href']) ? $field['href'] : '#') . '" id="' . (isset($field['id']) ? $field['id'] : '')  . '">' . $field['value'] . '</button>';
                    continue;
                }
                //      BUTTON
                else  if ($field['type'] == 'button') { //rint_r($field);
                    //  if (isset($config['data'])) { //$data = $field['data']; } else { $data='';}
                    $formHtml .= '<button '.$data.' class="w-50 btn btn-lg btn-primary ' . $class . '" id="' .  (isset($field['id']) ? $field['id'] : '')  . '" ' . (isset($field['disabled']) ? $field['disabled'] : '') . ' >' . $field['value'] . '</button>';
                    continue;
                }
                //LINK
                else if ($field['type'] == 'link') {
                    $formHtml .= '<div class="link ' . $class . '"><a href="' . (isset($field['href']) ? $field['href'] : '#') . '" id="' . (isset($field['id']) ? $field['id'] : '') . '">' . (isset($field['text']) ? $field['text'] : '') . '</a></div>';
                    continue;
                } 
                //echo "HTML enn cours 2 : $formHtml<br>";

            }
            $isFloating = isset($config['float']) && $config['float'] == 1 && (!isset($field['type']) || ($field['type'] != 'checkbox' && $field['type'] != 'radio'));

            if ($isFloating) {
                $formHtml .= '<div class="form-floating mb-3">';
            } else {
                $formHtml .= '<div class="form-group">';
            }
            $labelPosition = isset($config['label']) ? $config['label'] : 'up';
            //echo "HTML enn cours 3: $formHtml<br>";

            //      LABEL INDEX
            if ((isset($field['label']) && $labelPosition == 'up')&& ((isset($field["type"]))&&($field['type'] != 'checkbox')) ){
                $formHtml .= '<label for="' . (isset($field['id']) ? $field['id'] : '') . '">' . $field['label'] . '</label>';
            }

            $defaultValue = isset($field['name']) && isset($_SESSION[$field['name']]) ? $_SESSION[$field['name']] : "";

            //      RADIO
            if ((isset($field["type"]))&&($field['type'] == 'radio') ){
                if (isset($field['options']) && is_array($field['options'])) {
                    //print_r($field["options"]);
                    foreach ($field['options'] as $option) {
                        $checked = (isset($field['optdef']) && $field['optdef']) ? ' checked' : '';

                        $formHtml .= '<input type="radio" name="' . $option['name'] . '" value="' . $option['value'] . '" id="' . $option['id'] . '"' . $checked;
                        if (isset($option['disabled']) && $option['disabled']) {
                            $formHtml .= ' disabled';
                        }
                        $formHtml .= '>';
                        $formHtml .= '<label for="' . $option['id'] . '">' . $option['label'] . '</label>';
                    }
                }
                
            } 
            //      CHECKBOX
            elseif  ((isset($field["type"]))&&($field['type'] == 'checkbox')) {
                $checked = (isset($field['optdef']) && $field['optdef']) ? ' checked' : '';
                $formHtml .= '<input type="checkbox" class="form-check-input"  ' . (isset($field['disabled']) ? $field['disabled'] : '') . ' ' . $checked;
                if (isset($field['id'])) {
                    $formHtml .= ' id="' . $field['id'] . '"';
                }
                if (isset($field['name'])) {
                    $formHtml .= ' name="' . $field['name'] . '"';
                }
                $formHtml .= '>';  // Ferme la balise input
                if (isset($field['label'])) {
                   $formHtml .= '<label for="' . (isset($field['id']) ? $field['id'] : '') . '">' . $field['label'] . '</label>';
                }
            } 
            //      SELECT
            elseif ((isset($field["type"]))&&($field['type'] == 'select')) {
                $formHtml .= '<select class="form-control ' . $class . '"';
                if (isset($field['id'])) {
                    $formHtml .= ' id="' . $field['id'] . '"';
                }
                if (isset($field['name'])) {
                    $formHtml .= ' name="' . $field['name'] . '"';
                }
                if (isset($field['onchange'])) {
                    $formHtml .= ' onchange="' . $field['onchange'] . '"';
                }            
                $formHtml .= '>';
                if (isset ($field['firstopt']))
               { $formHtml .= '<option value="">' . $field['firstopt'] . '</option>';}
               foreach ($field['options'] as $option) {
                //print_r($option);
                // Comparaison directe entre l'ID de l'option et la valeur par défaut du champ
                $selected = ($option->id == $field['optdef']) ? ' selected' : '';
                $formHtml .= '<option value="' . $option->id . '"' . $selected . '>' . $option->valeur . '</option>';
            }
                $formHtml .= '</select>';
                //echo "HTML enn cours 4 - select : $formHtml<br>";

            } 
            //      INPUT TEXT
                // INPUT HIDDEN
            else if (isset($field['type']) && $field['type'] == 'hidden') {
                $formHtml .= '<input type="hidden"';
                if (isset($field['name'])) {
                    $formHtml .= ' name="' . $field['name'] . '"';
                }
                if (isset($field['value'])) {
                    $formHtml .= ' value="' . $field['value'] . '"';
                }
                if (isset($field['id'])) {
                    $formHtml .= ' id="' . $field['id'] . '"';
                }
                $formHtml .= '>'; // Ferme la balise input
            }
            else  if ((isset($field['type']))&&($field['type']!='textarea')&&($field['type']!='select')&&($field['type']!='checkbox')&&($field['type']!='radio')) {
                $formHtml .= '<input type="' . $field['type'] . '" class="form-control ' . $class . '" value="' . $defaultValue . '"';
    
                if (isset($field['id'])) {
                    $formHtml .= ' id="' . $field['id'] . '"';
                }
                if (isset($field['name'])) {
                    $formHtml .= ' name="' . $field['name'] . '"';
                }
                if (isset($field['placeholder']) && !$isFloating) {
                    $formHtml .= ' placeholder="' . $field['placeholder'] . '"';
                }
                if (isset($field['required']) && $field['required']) {
                    $formHtml .= ' required';
                }

                if (isset($field['disabled']) && $field['disabled']) {
                    $formHtml .= ' disabled';
                }
                $formHtml .= '>';
            }

            if (isset($field['type']) && $field['type'] == 'number') {
                $formHtml .= '<input type="number" class="form-control ' . $class . '"';
                if (isset($field['id'])) {
                    $formHtml .= ' id="' . $field['id'] . '"';
                }
                if (isset($field['name'])) {
                    $formHtml .= ' name="' . $field['name'] . '"';
                }
                if (isset($field['min'])) {
                    $formHtml .= ' min="' . $field['min'] . '"';
                }
                if (isset($field['max'])) {
                    $formHtml .= ' max="' . $field['max'] . '"';
                }
                if (isset($field['value'])) {
                    $formHtml .= ' value="' . $field['value'] . '"';
                }
                if (isset($field['autocomplete'])) {
                    $formHtml .= ' autocomplete="' . $field['autocomplete'] . '"';
                }
                if (isset($field['required']) && $field['required']) {
                    $formHtml .= ' required';
                }
                if (isset($field['disabled']) && $field['disabled']) {
                    $formHtml .= ' disabled';
                }
                $formHtml .= '>'; // Ferme la balise input
            }

            if (isset($field['label']) && ($labelPosition == 'down')&&($field['type'] != 'checkbox')) {
                $formHtml .= '<label for="' . (isset($field['id']) ? $field['id'] : '') . '">' . $field['label'] . '</label>';
            }

            if (isset($field['validation_message'])) {
                $formHtml .= '<div class="valid-feedback">' . $field['validation_message'] . '</div>';
            }

            $formHtml .= '</div>';  // Fermeture de .form-group ou .form-floating
        }
        //echo "HTML après ajout du bouton: $formHtml<br>";

        $formHtml .='</form>';
        if (isset($config['script'])) { $formHtml .= $config['script']; }
        //echo $formHtml;
        return $formHtml;
    }
}
