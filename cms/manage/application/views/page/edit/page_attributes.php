<? if (!empty($fields['attributes']) && count($fields['attributes'])): ?>

<div class="form_section">
    <div class="form_section_header">Attributes</div>
    <div class="section_content">

        <? 
        
        foreach ($fields['attributes'] as $key => $item) {

            $field_name = 'fields[attribute_values]['.$item['group_id'].']';
            
            // Start Attribute Row
            echo '<div class="form_row" id="'.$field_name.'_node">'
                .'<label>'.$item['title'].'</label>'
                .'<div class="field" id="'.$field_name.'_parent">';


            // In case there are empty attribute values
            if (!count($item['values'])) $item['values'] = array('');

            // Show the dropdowns
            foreach ($item['values'] as $i => $value) {

                $row_name   = $field_name .'['.$i.']';

                echo '<div id="'.$row_name.'_node" class="sub_row">';
                
                echo '<div class="field_text" id="'.$row_name.'">'
                    . form_dropdown($row_name, $item['options'], $value, 'class="style_attribute" data-group="'.$item['group_id'].'"')
                    .'</div>';

                if (!empty($item['multi']) && ($item['multi'] == 'yes')) {
                    echo '<div class="field_buttons">'
                        .'<a href="#" onclick="TNDR.Form.Actions.dupeTemplate(this, \''.$field_name.'\', \'sub_row\'); return false;"><img width="10" height="10" src="/cms/manage/assets/img/mini_icons/plus.gif" alt="ADD"></a>'
                        .'<a href="#" onclick="TNDR.Form.Actions.remove(\''.$row_name.'\'); return false;"><img width="10" height="10" src="/cms/manage/assets/img/mini_icons/minus.gif" alt="DELETE"></a>'
                        .'</div>';
                }
                
                echo '</div>';
            
            }
            
            // Template
            if (!empty($item['multi']) && ($item['multi'] == 'yes')) {
                
                $row_name   = $field_name .'[%%]';

                echo '<script id="'.$field_name.'_template" type="text/html">';
                echo '<div id="'.$row_name.'_node" class="sub_row">';
                echo '<div class="field_text" id="'.$row_name.'">'
                    . form_dropdown($row_name, $item['options'], $value, 'class="style_attribute" data-group="'.$item['group_id'].'"')
                    .'</div>';
                if (!empty($item['multi']) && ($item['multi'] == 'yes')) {
                    echo '<div class="field_buttons">'
                        .'<a href="#" onclick="TNDR.Form.Actions.dupeTemplate(this, \''.$field_name.'\', \'sub_row\'); return false;"><img width="10" height="10" src="/cms/manage/assets/img/mini_icons/plus.gif" alt="ADD"></a>'
                        .'<a href="#" onclick="TNDR.Form.Actions.remove(\''.$row_name.'\'); return false;"><img width="10" height="10" src="/cms/manage/assets/img/mini_icons/minus.gif" alt="DELETE"></a>'
                        .'</div>';
                }
                echo '</div>';
                echo '</script>';
                
            
            }

            
            // End Attribute Row
            echo '</div>'
                .'</div>';

        } 
        
        ?>

    </div>
</div>

<? endif; ?>