<?php

class pluginOpenGraphRemixed extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'defaultImage'=>'',
			'fbAppId'=>''
		);
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Default image').'</label>';
		$html .= '<input id="jsdefaultImage" name="defaultImage" type="text" value="'.$this->getValue('defaultImage').'" placeholder="https://">';
		$html .= '<span class="tip">'.$L->g('set-a-default-image-for-content').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Facebook App ID').'</label>';
		$html .= '<input name="fbAppId" type="text" value="'.$this->getValue('fbAppId').'" placeholder="App ID">';
		$html .= '<span class="tip">'.$L->g('set-your-facebook-app-id').'</span>';
		$html .= '</div>';

		return $html;
	}

	public function siteHead()
	{
		global $url;
		global $site;
		global $WHERE_AM_I;
		global $page;
		global $content;

		$og = array(
			'locale'	    =>$site->locale(),
			'type'		    =>'website',
			'title'		    =>$site->slogan(),
			'description'	=>$site->description(),
			'url'		    =>$site->url(),
			'image'		    =>'',
			'siteName'	    =>$site->title()
		);

		switch ($WHERE_AM_I) {
			// The user filter by page
			case 'page':
				$og['type']		    = 'article';
				$og['title']		= $page->title();
				$og['description']	= $page->description();
				$og['url']		    = $page->permalink($absolute=true);
				$og['image'] 		= $page->coverImage($absolute=true);
                $og['author']       = $page->user('firstname').' '.$page->user('lastname');
                $og['published']    = $page->dateRaw();
                $og['section']      = $page->category();
                $og['tag']          = $page->tags(true);

				$pageContent = $page->content();
				break;
                
			// The user filter by category
			case 'category':
                // Get the category key from the URL
                $categoryKey = $url->slug();

                // Create the Category-Object
                $category = new Category($categoryKey); 
                
				$og['title']		= $category->name();
				$og['description']	= $category->description();
				$og['url']		    = $category->permalink($absolute=true);
                
                $pageContent = '';
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				}
				elseif (isset($content[0]) ) {
					$og['image'] 	= $content[0]->coverImage($absolute=true);
					$pageContent 	= $content[0]->content();
				}
				break;
                
			// The user is in the homepage
			default:
				$pageContent = '';
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				}
				elseif (isset($content[0]) ) {
					$og['image'] 	= $content[0]->coverImage($absolute=true);
					$pageContent 	= $content[0]->content();
				}
				break;
		}

		$html  = PHP_EOL.'<!-- Open Graph -->'.PHP_EOL;
		$html .= '<meta property="og:locale" content="'.$og['locale'].'">'.PHP_EOL;
		$html .= '<meta property="og:type" content="'.$og['type'].'">'.PHP_EOL;
        $html .= '<meta property="og:title" content="'.$og['title'].'">'.PHP_EOL;
        if($og['description'])
		  $html .= '<meta property="og:description" content="'.$og['description'].'">'.PHP_EOL;
		$html .= '<meta property="og:url" content="'.$og['url'].'">'.PHP_EOL;
		$html .= '<meta property="og:site_name" content="'.$og['siteName'].'">'.PHP_EOL;
        
        if($og['type'] == 'article') {
            $html .= '<meta property="article:author" content="'.$og['author'].'">'.PHP_EOL;
            $html .= '<meta property="article:published_time" content="'.$og['published'].'">'.PHP_EOL;
            if($og['section'])
                $html .= '<meta property="article:section" content="'.$og['section'].'">'.PHP_EOL;
            foreach ($og['tag'] as $tagKey=>$tagName) {
                $html .= '<meta property="article:tag" content="'.$tagName.'">'.PHP_EOL;
            }
            
        }

		// If the page doesn't have a coverImage try to get an image from the HTML content
		if (empty($og['image'])) {
			// Get the image from the content
			$src = DOM::getFirstImage($pageContent);
			if ($src!==false) {
				$og['image'] = $src;
			} else {
				if (Text::isNotEmpty($this->getValue('defaultImage'))) {
					$og['image'] = $this->getValue('defaultImage');
				}
			}
		}

		if ($og['image']) {
            $html .= '<meta property="og:image" content="'.$og['image'].'">'.PHP_EOL;
            if(strpos($og['image'],"https://")!==false)
                $html .= '<meta property="og:image:secure_url" content="'.$og['image'].'">'.PHP_EOL;

            $imageDetail = getimagesize($og['image']);
            $html .= '<meta property="og:image:type" content="'.$imageDetail['mime'].'">'.PHP_EOL;
            $html .= '<meta property="og:image:width" content="'.$imageDetail['0'].'">'.PHP_EOL;
            $html .= '<meta property="og:image:height" content="'.$imageDetail['1'].'">'.PHP_EOL;
        }
        
		if (Text::isNotEmpty($this->getValue('fbAppId'))) {
			$html .= '<meta property="fb:app_id" content="'. $this->getValue('fbAppId').'">'.PHP_EOL;
		}

        return $html;
	}

}