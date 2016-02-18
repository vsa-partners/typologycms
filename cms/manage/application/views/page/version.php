<?

function outputNode($node=null) {

		$output 		= '';
		$row_template 	= '<tr><th width="65">%s</th><td>%s</td></tr>';
		$table_template	= '<table width="100%%" class="data_table small" cellspacing="0" cellpadding="0">%s</table>';
	
		if (is_null($node)) {
			return '';
		} else if (is_array($node)) {
			
			$array_output	= '';
			
			foreach ($node as $key => $val) {
			
				if ($key == 'data') {
					$array_output .= sprintf($row_template, $key, htmlentities($val));
				} else {
					$array_output .= sprintf($row_template, $key, outputNode($val));
				}

			}

			$output .= sprintf($table_template, $array_output);

		} else {
			
			$output .= $node;
		
		}
		
		return $output;

}

function outputXML($node_name=null, $node=null, $level=1) {

		$output 		= '';
		$row_template 	= '<tr><th width="65">%s</th><td>%s</td></tr>';
		$table_template	= '<table width="100%%" class="data_table show_border small" cellspacing="0" cellpadding="0">%s</table>';

		$node_data		= '';

		if (count($node->children())) {

			$child_data = '';

			foreach ($node->children() as $child_name => $child) {
				$child_data	.= outputXML($child_name, $child, 2);
			}			

			$node_data .= sprintf($table_template, $child_data);

		} else if (count($node->attributes())) {

			$child_data = '';

			foreach ($node->attributes() as $child_name => $child) {
				$child_data	.= outputXML($child_name, $child, 2);
			}			

			$node_data .= sprintf($table_template, $child_data);


		} else {

			$node_data = (string) $node;
		
		}

		if ($level == 1) {
			$output = $node_data;
		} else {
			$output = sprintf($row_template, $node_name, $node_data);		
		}

		return $output;
}


$page_info = $version;
unset($page_info['content']);

$xml = simplexml_load_string($version['content'], 'SimpleXMLElement', LIBXML_NOCDATA);

?>

<div style="padding-right: 10px; width: 590px; height: 450px; overflow: hidden; overflow-y: auto;">

	<div id="popup_header">
		Page <?=$version['page_id']?> / Version Id <?=$version['version_id']?>
	</div>


	<div class="tndr_form">
	
		<div class="form_section">
			<div class="form_section_header">Content</div>
			<div class="form_section_content">
				<?=outputXML('__ROOT__', $xml);?>
			</div>
		</div>
	
		<div class="form_section form_section_closed">
			<div class="form_section_header"><a href="#" onClick="TNDR.Form.Actions.toggleSection(this);">Content XML</a></div>
			<div class="form_section_content">
				<?='<pre>'.htmlentities($version['content']) . '</pre>'?>
			</div>
		</div>
	
		<div class="form_section form_section_closed">
			<div class="form_section_header"><a href="#" onClick="TNDR.Form.Actions.toggleSection(this);">Page Info</a></div>
			<div class="form_section_content">
				<?=outputNode($page_info)?>		
			</div>
		</div>
	
	</div>

</div>
