<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<!-- This sitemap is being generated automatically -->
	<!-- Generated <?=date('Y-m-d H:i:s')?> -->

	<? foreach  ($pages as $page) {
	
		if(!strlen($page['path']) || ($page['type'] == 'root')) continue;

		if(!empty($page['options']['include_sitemap']) && ($page['options']['include_sitemap'] == 'no')) continue;
	
		$url = 'http://' . reduce_multiples($_SERVER['HTTP_HOST'] .'/'. SITEPATH . $page['path'] . '/', '/');
		$mod = (strlen($page['publish_date']) && ($page['publish_date'] != '0000-00-00 00:00:00')) ? date('Y-m-d', strtotime($page['publish_date'])) : '';	
		
		echo '<url>'
			.'<loc>'.$url.'</loc>'
			.'<lastmod>'.$mod.'</lastmod>'
			//.'<id>'.$page['page_id'].'</id>'
			.'</url>';
			
	} ?>

</urlset>
