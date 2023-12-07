<?php
/* * * *
 * Class: FormBuilder
 * Version: 12
 * Date: 7 December, 2023
 * Description: Creates form fields, bugfixes in the version
 * Fields: 'text','password', 'textarea', 'email', 'checkbox', 'radio', 'select','countries','kvselect' 'multiselect', 'multiple', 'date','date_special', 'image','wp_color', 'wp_upload', 'wp_upload_multiple','range', 'submit', 'button', 'reset'
 * * * * */
class FormBuilder
{
    
    public $args_help = array(
        'type'  => 'THE TYPE OF THE FIELD, IF LEFT SHALL ASSUMED input type text<hr/>',
        'name'  => 'THE NAME',
        'id'    => 'THE ID',
        'disabled' => 'TO DISABLE THE FIELD',
        'readonly' => 'TO MAKE THE FIELD READONLY',
        'label OR placeholder' => 'THE LABEL OR PLACEHOLDER',
        'NOTE FOR THE ABOVE 3' => 'IF ANY ONE OF THE name/id/label WAS PROVIDED, IT WILL TAKE CARE OF THE REST<hr/>',
        'label_col' => 'class of the label column for example col-md-4',
        'input_col' => 'class of the input column for example col-md-8',
        'required OR req' => 'IS REQUIRED OR NOT',
        'type_class' => 'alpha:alphabets only,numeric: numbers only,alphanumeric: exclude all special chars',
        'description' => 'DESCRIPTION DIV BELOW THE INPUT',
        'dbval' => 'THE VALUE COMING FROM DATABSE',
        'container_class' => 'CSS CLASS OF THE ABOVE div CONTINER',
        'options' => 'THE OPTIONS FOR: radio/select/multiple',
        'for range' => 'min & max: ARE MUST',
        'input_class' => 'APPLY COLUMN CLASSES DIRECT TO input EXCEPT ceheckbox/radiobutton/fileupload/multiple', //
        'NOTE:' => 'Want jQuery Validation? must add custom js functinos or libraries as this is a php-only class yet.',
        'Available types in this version' => " 'text', 'password', 'textarea', 'email', 'checkbox', 'radio', 'select', 'countries', 'kvselect', 'multiselect', 'multiple', 'date', 'date-special', 'image', 'range', 'wp_color', 'wp_upload', 'wp_upload_multiple','submit' ",
    );

    /****************************|THE FIELD|******************************/

    public function field($args)
    {

        foreach ($args as $k => $v) {
            $$k = $v;
        }
        $types = array('text', 'password', 'textarea', 'email', 'checkbox', 'radio', 'select', 'countries', 'kvselect', 'multiselect', 'multiple', 'date', 'date-special', 'image', 'range', 'wp_color', 'wp_upload', 'wp_upload_multiple', 'button', 'submit');

        if ((!isset($name) || empty($name))) {
            echo "<div class='text-danger form-group row'>Please specify field name.</div>";
            return;
        } elseif ((!isset($id) || empty($id))) {
            echo "<div class='text-danger form-group row'>Please specify field id.</div>";
            return;
        } elseif (empty($label) && (!isset($label))) {
            $args['label']  = '';
        } elseif (!empty($label) && (isset($label))) {
            $args['label']   =      ucfirst($this->make_label($label));
        } 
        
        $the_id     = $id;
        $the_name   = $name;

        
        $args['the_id']      =      $id;
        $args['the_name']    =      $name;
?>
        <?php
         /*TO AVOID ERRORS*/
         $type = isset($type) && !empty($type) ? $type :'text';
         $container_class = isset($container_class) && !empty($container_class) ? $container_class :'';
        @$placeholder = isset($args['placeholder']) && !empty($args['placeholder']) ? $args['placeholder'] :(isset($label) && !empty($label) ? $label :'');
        $label = isset($args['label']) && !empty($args['label']) ? $args['label'] :(isset($label) && !empty($label) ? $label :'');
        if ($type === "range") {
            if (($min == "") || ($min == "")) {
                echo "<div class='text-danger form-group row'>Please specify min & max values for range.</div>";
                return;
            }
        }
        ?>
        <div class="form-group form_builder_row <?php echo $type ?>_container <?php echo $the_id ?>_container <?php echo $container_class != "" ? $container_class : ''; ?> <?php echo ( isset($args['is_featured']) ) ? 'is_featured' : 'is_not_featured'; ?>">
            <?php if (isset($label_col) && $label_col != "") { ?><div class="<?php echo $label_col ?> form_builder_col"><?php } ?>
                <?php if ($type != "submit" && $type != "button") : ?>
                    <?php if (!empty($label) && (isset($label))) {?>
                    <label class="control-label <?php echo @$label_class != "" ? $label_class : ''; ?> <?= $the_name; ?>_label <?php echo ($type != 'checkbox') ? '' : 'order-md-2'; ?>" for="<?php echo $the_id; ?>">
                        <?php if (($type == 'checkbox' || $type == 'radio')) : ?><span><?php endif; ?><?= @$label; ?>:<?php if (($type == 'checkbox' || $type == 'radio')) : ?></span><?php endif; ?>
                        <?php echo (isset($required) || @$required !== null || !empty(@$required) ? '<span class="text-danger">*</span>' : ''); ?>
                    </label>
                    <?php }?>
                <?php endif; //ends type=submit
                ?>
                <?php if (isset($label_col) && $label_col != "") { ?>
                </div><?php } ?>
            <?php if (isset($input_col) && $input_col != "") { ?><div class="<?php echo $input_col ?> form_builder_col <?php echo (isset($args['is_featured'])) ? 'is_featured' : 'is_src'; ?>"><?php } ?>
                <?php
                if (in_array($type, $types)) {
                    $this->$type($args);
                } else {
                    $this->text($args);
                }
                ?>
                <?php if (isset($input_col) && $input_col != "") { ?></div><?php } ?>
        </div>
        <?php if (!empty(@$help)) : ?>
            <?php $this->help(); ?>
        <?php endif; ?>
    <?php
    }

    public function make_label($str)
    {
        // Remove all characters except A-Z, a-z, 0-9
        $str = preg_replace('/[^A-Za-z0-9 -_]/', ' ', $str);
        // Replace sequences of spaces
        $str = preg_replace('/  */', ' ', $str);
        $str = preg_replace('/-/', ' ', $str);
        $str = preg_replace('/_/', ' ', $str);
        return ucfirst($str);
    }

    /**********************************************************************************************************************/
    /******************************** | THE FIELDS | *********************************************************************/
    /**********************************************************************************************************************/


    /*  
 * 1. TEXT
 * * * * * * * * */
    public function text($args)
    {
        global $type ;
        global $container_class ;
        global $placeholder ;
        
        foreach ($args as $k => $v) {
            $$k = $v;
        }
       
    ?>
        <input type="text" 
        class="<?php echo !empty($type_class) && $type_class != "" ? $type_class : ''; ?>
            <?php echo (@$required != "" ? 'required' : ''); ?> 
            form_builder_field form-control <?php echo !empty($input_class) ? $input_class : ''; ?> <?php echo $the_id ?> 
            input-<?= $type; ?> 
            field_<?= $the_id ?>" 
        id="<?= $the_id ?>" 
        name="<?= $the_name ?>" 
        value="<?php echo (!empty(@$dbval) ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?>" 
        <?= (@$required != "" ? 'required="required"' : ''); ?> 
        placeholder="<?php echo !empty($placeholder) ? ucfirst($placeholder) : ''; ?>" 
        data-id="<?= $the_id ?>" 
        minlength="<?= (isset($min) && $min != "") ? $min : ''; ?>" 
        maxlength="<?= (isset($max) && $max != "") ? $max : ''; ?>" 
        <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> 
        <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }


    /*  
 * 2. TEXTAREA
 * * * * * * * * */
    public function textarea($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <textarea class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> form-control <?= (!empty($input_class) ? $input_class : ''); ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> placeholder="<?=(!empty($placeholder) ? $placeholder : ''); ?>" data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>><?= (@$dbval != "" ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?></textarea>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? $description : ''; ?></small>
    <?php
    }


    /*  
 * 3. EMAIL
 * * * * * * * * */
    public function email($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <input type="email" 
        class="form_builder_field 
        <?= (@$required != "" ? 'required' : ''); ?>
         form-control <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" value="<?= (@$dbval != "" ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> placeholder="<?php echo !empty($placeholder) ? ucfirst($placeholder) : ''; ?>" data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



    /*  
 * 4. CHECKBOX
 * * * * * * * * */
    public function checkbox($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <?php if (@!empty($input_class)) { ?>
            <div class="order-md-1 <?= @$input_class; ?> display-table">
            <?php } ?>

            <input type="checkbox" class=" form_builder_field <?php echo (!empty($required) ? 'required' : ''); ?>
  form-control  <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?> table-cell" id="<?= $the_id ?>" name="<?= $the_name ?>" value="yes" <?= (@$required != "" ? 'required="required"' : ''); ?> data-id="<?= $the_id ?>" <?php echo (@$dbval == "yes" ? 'checked="checked"' : (isset($_REQUEST[$the_name]) ? 'checked="checked"' : '')); ?> style=" display:table-cell;" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <?php if (@!empty($input_class)) { ?>
            </div>
        <?php } ?>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }


    /*  
 * 5. RADIO BUTTON
 * * * * * * * * */
    public function radio($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <?php foreach ($options as $option) : ?>
            <label class="<?= !empty($input_class) ? $input_class : ''; ?> ">
            <input <?php echo (!empty($required) ? 'required' : ''); ?>
             type="radio" class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> 
             <?= $the_id ?> input-<?= $type; ?> 
             field_<?= $the_id ?>" 
             id="<?= $option; ?>" 
             name="<?= @$the_name ?>" 
             <?php echo (@$dbval == @$option ? 'checked="checked"' : (isset($_REQUEST[@$the_name]) && $_REQUEST[@$the_name] == @$option ? 'checked="checked"' : '')); ?> data-id="<?= @$the_name ?>" value="<?= $option; ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
                <span><?= ucfirst(@$option); ?></span>
            </label>
        <?php endforeach; ?>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }




    /*  
 * 6. SELECT
 * * * * * * * * */
    public function select($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <select class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?> form-control" id="<?= $the_id ?>" name="<?= $the_name ?>" data-id="<?= $the_name ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <option value="">Select <?php echo $_REQUEST[$the_name]; ?></option>
            <?php foreach (@$options as $option) : if ($option === "") continue; ?>
                <option value="<?= (@$option); ?>" <?php echo (@$dbval == @$option ? 'selected="selected"' : ''); ?>><?= ucfirst(@$option); ?></option>
            <?php endforeach; ?>
        </select>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }




    /*  
 * 7. MULTISELECT
 * * * * * * * * */
    public function multiselect($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <select class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?> form-control" id="<?= $the_id ?>" name="<?= $the_name ?>[]" multiple="multiple" data-id="<?= $the_name ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <option></option>
            <?php foreach (@$options as $option) : ?>
                <option value="<?= (@$option); ?>" <?php echo (@$dbval == $option ? 'selected="selected"' : (isset($_REQUEST[@$the_name]) && in_array($option, $_REQUEST[@$the_name]) ? 'selected="selected"' : '')); ?>><?= ucfirst(@$option); ?></option>
            <?php endforeach; ?>
        </select>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }


    /*  
 * 8. MULTIPLE
 * * * * * * * * */
    public function multiple($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <?php foreach (@$options as $option) : if (empty($option)) {
                continue;
            } ?>
            <label class="<?= !empty($input_class) ? $input_class : ''; ?> ">
                <input type="checkbox" class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> <?= $the_id ?> input-<?= $type; ?> input-checkbox field_<?= $the_id ?>" id="<?= $option; ?>" name="<?= $the_name ?>[]" <?php echo (@$dbval == $option ? 'checked="checked"' : (isset($_REQUEST[@$the_name]) && in_array($option, $_REQUEST[@$the_name]) ? 'checked="checked"' : '')); ?> data-id="<?= $the_name ?>" value="<?= $option; ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
                <span><?= ucfirst(@$option); ?></span>
            </label>
        <?php endforeach; ?>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



/* 
 * 9. COUNTRIES
 * * * * * * * * */
    public function countries($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <?php $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"); ?>
        <select class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> type-select field_<?= $the_id ?> form-control" id="<?= $the_id ?>" name="<?= $the_name ?>" data-id="<?= $the_name ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <option></option>
            <?php foreach (@$countries as $country) : ?>
                <option value="<?= (@$country); ?>" <?php echo (@$dbval == @$country ? 'selected="selected"' : ((isset($_REQUEST[@$the_name]) && $_REQUEST[@$the_name] == @$country) ? 'selected="selected"' : '')); ?>><?= ucfirst(@$country); ?></option>
            <?php endforeach; ?>
        </select>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



/* 
 * 10. PASSWORD
 * * * * * * * * */
    public function password($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <input type="password" class="<?= isset($type_class) && $type_class != "" ? $type_class : ''; ?><?= (@$required != "" ? 'required' : ''); ?> 
  form_builder_field form-control <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> 
  input-<?= $type; ?> field_<?= $the_id ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" value="<?= (@$dbval != "" ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> placeholder="<?php echo !empty($placeholder) ? ucfirst($placeholder) : ''; ?>" data-id="<?= $the_id ?>" minlength="<?= (isset($min) && $min != "") ? $min : ''; ?>" maxlength="<?= (isset($max) && $max != "") ? $max : ''; ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



/* 
 * 11. DATE
 * * * * * * * * */
    public function date($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <div class="<?= !empty($input_class) ? $input_class : ''; ?>">

            <?php if (isset($dbval)) {
                $time = strtotime($dbval);
                $the_date = date('d-m-Y', $time);
            } elseif (isset($_REQUEST[$the_name])) {
                $time = strtotime($_REQUEST[$the_name]);
                $the_date = date('d-m-Y', $time);
            } ?>
            <input type="date" class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> form-control <?= $the_id ?> input-<?= $type; ?> type-date field_<?= $the_id ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" value="<?= (@$dbval != "" ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> data-id="<?= $the_id ?>" data-date="<?= $the_date; ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
        </div>
        <small class="description response font-italic text-small text-muted pl-1">
                <?php echo  !empty($description) ? ucfirst($description) : ''; ?>
                    </small>
    <?php
    }


/* 
 * 12. SELECT KEY VALUE
 * * * * * * * * */
    public function kvselect($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <select class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> type-select kvselect field_<?= $the_id ?> form-control" id="<?= $the_id ?>" name="<?= $the_name ?>" data-id="<?= $the_name ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <option></option>
            <?php foreach (@$options as $k => $v) : ?>
                <option value="<?= (@$k); ?>" <?php echo (@$dbval == @$k ? 'selected="selected"' : ((isset($_REQUEST[@$the_name]) && $_REQUEST[@$the_name] == @$k) ? 'selected="selected"' : '')); ?>><?= ucfirst(@$v); ?></option>
            <?php endforeach; ?>
        </select>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



/* 
 * 13. IMAGE
 * * * * * * * * */
    public function image($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <div class="<?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>">
            <input type="hidden" value="<?= (@$dbval != "" ? @$dbval : @$val); ?>" id="<?= $the_name ?>_value" name="<?= $the_name ?>_value" class="img_src hidden" data-id="<?= $the_id ?>">
            <input type="file" class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> 
    <?= $the_name ?>_file btn btn-outline pull-left" id="<?= $the_id ?>" name="<?= $the_name ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> accept="image/*" style="width:70%;" onChange="document.getElementById('<?= $the_id ?>_preview').src = window.URL.createObjectURL(this.files[0]);document.getElementById('<?= $the_id ?>_preview').style.display = 'block'" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <!-- <button type="button" id="<?= $the_name ?>_btn" class="btn btn-info btn-upload">Upload</button> -->
            <img src="<?= (@$dbval != "" ? $dbval : ''); ?>" id="<?= $the_name ?>_preview" class="img-disp pull-right img-thumbnail" style="max-width:90px;display:none;" />
            <div class="col-sm-12 text-danger" id="<?= $the_name ?>_photo_resp"></div>
            <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
        </div>
    <?php
    }



    /* 
 * 14. DATE SPECIAL
 * * * * * * * * */
    public function date_special($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <div class="<?= !empty($input_class) ? $input_class : ''; ?>">

            <?php if (isset($dbval)) {
                $time = strtotime($dbval);
                $the_date = date('d-m-Y', $time);
            } elseif (isset($_REQUEST[$the_name])) {
                $time = strtotime($_REQUEST[$the_name]);
                $the_date = date('d-m-Y', $time);
            } ?>
            <input type="text" class="form_builder_field <?= (@$required != "" ? 'required' : ''); ?> form-control <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" value="<?= (@$dbval != "" ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> data-id="<?= $the_id ?>" data-date="<?= $the_date; ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
            <script>
                (function($) {
                    //$('head').append('<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript" />');
                    $('head').append('<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />');

                    $('#<?= $the_name ?>').datepicker({
                        uiLibrary: 'bootstrap4',
                        format: 'dd mmm yyyy'
                    });

                })(jQuery);
            </script>
        </div>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



    /* 
 * 15. RANGE
 * * * * * * * * */
    public function range($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <div class="<?= !empty($input_class) ? $input_class : ''; ?>">
            <input type="range" class="form_builder_field  form-control <?= (@$required != "" ? 'required' : ''); ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>" name="weight" id="<?= $the_id ?>" value="<?= (@$dbval != "" ? $dbval : (isset($_REQUEST[$the_name]) ? $_REQUEST[$the_name] : '')); ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> min="<?= @$min ?>" max="<?= @$max ?>" oninput="range_weight_disp.value = <?= $the_name ?>.value" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
            <output id="range_weight_disp"><?= $_POST[$the_name]; ?></output>
            <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
        </div>
    <?php
    }


    /* 
 * 16. WORDPRESS COLOR
 * * * * * * * * */
    public function color($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <input type="text" class="cpa-color-picker <?= isset($type_class) && $type_class != "" ? $type_class : ''; ?><?= (@$required != "" ? 'required' : ''); ?> form_builder_field form-control <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" value="<?= (@$dbval != "" ? $dbval : ''); ?>" <?= (@$required != "" ? 'required="required"' : ''); ?> data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



    /* 
 * 17. WORDPRESS UPLOAD
 * * * * * * * * */

    public function wp_upload($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
        // is_featured : means it'll only return the id throgh which the featured image shall be set
    ?>
	<!-----<div class="d_flex">---->
        <input type="text" <?php echo (isset($args['is_free_link'])) ? '' : 'readonly'; ?> name="<?= $the_name ?>" id="<?= $the_id ?>" value="<?= ($dbval  != "") ? ($dbval) : ''; ?>" class="input_text  <?= isset($type_class) && $type_class != "" ? $type_class : ''; ?><?= (@$required != "" ? 'required' : ''); ?> form_builder_field <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?> <?php echo (isset($args['is_featured'])) ? 'd-none' : ''; ?>" />
        <input class="btn btn-primary upl_btn" type="button" name="<?= $the_id ?>" id="<?= $the_id ?>-btn" class="btn btn-primary" value="Upload Image" data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>>
        <div class="<?= $the_id ?>_imagearea img_cont ">
            <div class="images_parent_class">
                <div class="<?= $the_id ?>-delete delete_btn">X</div>
                <img src="<?= ($dbval  != "") ? ($dbval) : ''; ?>" id="<?= $the_id ?>-img" class="<?= ($dbval  != "") ? '' : ''; ?>" style="margin-left: auto; margin-right: auto; display: block;" />
            </div>
        </div>

        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
        <script>
            var $ = jQuery;
            jQuery(document).ready(function($) {
                $("#<?= $the_id ?>-btn").click(function(e) {
                    e.preventDefault();
                    var image = wp.media({
                            title: "Upload Image",
                            // mutiple: true if you want to upload multiple files at once
                            multiple: false
                        }).open()
                        .on("select", function(e) {
                            // This will return the selected image from the Media Uploader, the result is an object
                            var uploaded_image = image.state().get("selection").first();
                            // We convert uploaded_image to a JSON object to make accessing it easier
                            // Output to the console uploaded_image
                            console.log(uploaded_image);
                            var image_url = uploaded_image.toJSON().url;
                            var image_id = uploaded_image.toJSON().id;
                            // Lets assign the url value to the input field

                            <?php if (isset($args['is_featured'])) : ?>
                                $('#<?= $the_id ?>').val(image_id);
                            <?php else : ?>
                                $('#<?= $the_id ?>').val(image_url);
                            <?php endif; ?>

                            $('#<?= $the_id ?>-img').attr('src', image_url);
                            $('#<?= $the_id ?>-img').addClass();
                            $('.<?= $the_id ?>-delete').show();
                        });
                });
            });
            $(document).on('click', '.<?= $the_id ?>-delete', function() {
                $('#<?= $the_id ?>').val('');
                $('#<?= $the_id ?>-img').attr('src', '');
                $('.<?= $the_id ?>-delete').hide();
            });
        </script>
        <style>
            .<?= $the_id ?>-delete {
                display: none;
                <?= ($dbval != "") ? 'display: block;' : '' ?>color: white;
                font-weight: bold;
                cursor: pointer;
                position: absolute;
                top: -10px;
                right: -10px;
                border: 2px solid red;
                border-radius: 50px;
                text-align: center;
                width: 18px;
                height: 18px;
                background-color: red;
            }

            .images_parent_class {
                display: inline-block;
                position: relative;
                width: 100px;
                height: 100px;
            }

            .images_parent_class img {
                max-width: 100%;
                width: 100%;
                height: auto;
            }

            /*.<?= $the_id ?>_imagearea {
                float: right;
            }*/
			.is_not_featured label {
    position: relative;
    clear: both;
    width: 100%;
    display: block;
}
.is_not_featured .input_text, .is_not_featured .upl_btn, .is_not_featured .img_cont{float:left}
.wp_upload_container:not(.featured_image_container) > .btn{top:0;float:left;}
.is_not_featured .input_text, .is_not_featured .img_cont{max-with:38%;with:38%;} .is_not_featured .upl_btn{max-with:24%;}
.img_cont{float:right !important;}
        </style>
		<!-----</div>---->
    <?php
    }

/* 
 * 18. WORDPRESS UPLOAD MULTIPLE IMAGES
 * * * * * * * * */
    public function wp_upload_multiple($args)
    {

        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <?php
        if (isset($dbval)) :
            $image_gallery = [];
            $all_ids = [];
            foreach ($dbval as $img_ids) :
                $img_ids_all = explode(',', $img_ids);

                foreach ($img_ids_all  as $img_id) :
                    // print_r($img_id);
                    if ($img_id === " ") {
                        continue;
                    }
                    $img_atts = wp_get_attachment_image_src($img_id, $default);
                    $img_src = $img_atts[0];
                    $image_gallery[$img_id] = $img_src;
                    $all_ids[]  = $img_id;
        ?>
                <?php endforeach; ?>
        <?php endforeach;
        endif;
        /*echo '<pre>';
        print_r($image_gallery);
        echo '<hr/>';
        print_r($all_ids);
        echo '</pre>'; */ ?>
        <div class="row">
            <div class="col-4">
                <input style="display:none;" id="<?= $the_id ?>" type="text" size="36" name="<?= $the_name ?>[]" value="<?= ($dbval  != "") ? (implode(',', $all_ids)) : ''; ?>" />
                <input id="<?= $the_id ?>_button" class="button <?= isset($type_class) && $type_class != "" ? $type_class : ''; ?><?= (@$required != "" ? 'required' : ''); ?>  <?= !empty($input_class) ? $input_class : ''; ?> <?= $the_id ?> input-<?= $type; ?> field_<?= $the_id ?>" type="button" value="Add <?php echo $label ?>" />
                <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
            </div>
            <div class="col-8">
                <div id="gallery_output" class="gallery_output">

                    <?php foreach ($image_gallery as $id => $src) : ?>
                        <div class="img-container">
                            <img src="<?php echo $src ?>" class="gallery_image img-thumbnail" id="<?php echo $id; ?>">
                            <button type="button" class="btncls">
                        <i class="fa fa-times"></i>
						<!---<i class="fa fa-close"></i>-->
                        	</button>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>


            <?php
            add_action('admin_enqueue_scripts', 'my_admin_scripts');

            function my_admin_scripts()
            {

                wp_enqueue_media();
                // wp_register_script('my-admin-js', WP_PLUGIN_URL . '/my-plugin/my-admin.js', array('jquery'));
                wp_enqueue_script('my-admin-js');
            }

            ?>
            <style>
                #gallery_output {
                    position: relative;
                }

                #gallery_output img {
                    max-height: 90px;
                    max-width: 90px;
                    margin: 5px;
                }

                #gallery_output .img-container {
                    float: left;
                    margin: 5px;
                    max-width: 92px;
                    max-height: 92px;
                }

                #gallery_output .img-container img,
                #gallery_output .img-container button {
                    display: table;
                }

                #gallery_output .img-container button {
                    background: #000;
                    border: none;
                    border-radius: 50%;
                    color: #FFF;
                    width: 16px;
                    height: 16px;
                    padding: 0px 0;
                    font-size: 12px;
                    float: right;
                    top: -12px;
                    position: relative;
                }
            </style>
            <script>
                jQuery(document).ready(function($) {
                    var custom_uploader;
                    $('#<?= $the_id ?>_button').click(function(e) {
                        e.preventDefault();
                        //If the uploader object has already been created, reopen the dialog
                        if (custom_uploader) {
                            custom_uploader.open();
                            return;
                        }

                        //Extend the wp.media object
                        custom_uploader = wp.media.frames.file_frame = wp.media({
                            title: 'Choose Image',
                            button: {
                                text: 'Choose Image'
                            },
                            multiple: true
                        });

                        //When a file is selected, grab the URL and set it as the text field's value
                        custom_uploader.on('select', function() {
                            let the_ids = [];
                            let the_gallery = "";
                            console.log(custom_uploader.state().get('selection').toJSON());
                            attachment = custom_uploader.state().get('selection').toJSON();

                            $.each(attachment, function(i, item) {
                                the_ids += item.id + ', ';
                                the_gallery += '<div class="img-container"><img src="' + item.url + '" class="gallery_image img-thumbnail" id="' + item.id + '"><button type="button" class="btncls"><i class="fa fa-close"></i></button></div>';
                            });
                            $('#<?= $the_id ?>').val(the_ids);
                            $('#gallery_output').html(the_gallery);
                        });

                        //Open the uploader dialog
                        custom_uploader.open();

                    });

                    $(document).on('click', '.btncls', function() {
                        let its_id = $(this).closest('.img-container').find('img').attr('id');
                        $(this).closest('.img-container').remove();
                        // alert(its_id);
                        let all_ids = [];
                        setTimeout(function() {
                            if ($('#gallery_output img').length === 0) {
                                $('#<?= $the_id ?>').val('');
                            }
                            $('#gallery_output img').each(function(item, i) {

                                all_ids += $(this).attr('id') + ',';
                                $('#<?= $the_id ?>').val(all_ids);

                            });
                        }, 2000);
                        // alert(all_ids);

                    });

                });
            </script>

        </div>
    <?php

    }


    /* 
 * 19. SUBMIT
 * * * * * * * * */
    public function submit($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <button type="submit" class="form_builder_submit submit_button button_<?= $the_id ?> button btn <?php echo !empty($class) ? $class: '';?>" id="<?= $the_id ?>" name="<?= $the_name ?>" data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>><?php echo @$label ?></button>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }



    /* 
 * 20. BUTTON
 * * * * * * * * */
    public function button($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <button type="button" class="form_builder_button type_button button_<?= $the_id ?> button btn <?php echo !empty($class) ? $class: ''; ?>" id="<?= $the_id ?>" name="<?= $the_name ?>" data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>><?= $label ?></button>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }





    /* 
 * 21. RESET
 * * * * * * * * */
    public function reset($args)
    {
        foreach ($args as $k => $v) {
            $$k = $v;
        }
    ?>
        <button type="reset" class="form_builder_reset type_reset button_<?= $the_id ?> button btn" id="<?= $the_id ?>" name="<?= $the_name ?>" data-id="<?= $the_id ?>" <?= (isset($readonly) && $readonly != "") ? 'readonly="readonly"' : ''; ?> <?= (isset($disabled) && $disabled != "") ? 'disabled="disabled"' : ''; ?>><?= $label ?></button>
        <small class="description response font-italic text-small text-muted pl-1"> <?php echo  !empty($description) ? ucfirst($description) : ''; ?></small>
    <?php
    }





    /* 
 * THE HELP
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function help()
    {
        ob_start(); ?>
        <div class="container">
            <div class="row">
                <h3 style="width:100%;">Help:</h3>
                <p><strong>Available args with notes:</strong></p>
                <ul>
                    <?php foreach ($this->args_help as $key => $value) : ?>
                        <li><strong><?= $key ?></strong> <?= $value; ?></li>
                    <?php endforeach; ?>
                </ul>
                <small><em>To hide this remove h or help from the function args</em></small>
            </div>
        </div>
<?php $html = ob_get_clean();
        echo $html;
    }
}

?>