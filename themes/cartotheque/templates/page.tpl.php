<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */

?>

  <div id="page-wrapper"><div id="page">

    <header class="container-fluid">
	<section class="container clearfix">
	 <ul class="topNav hidden-xs">
		<li class="home"><span><a href="<?php print $front_page; ?>" rel="home"></a></span><?php print t('Home'); ?></li>
		<?php
			if ($user->uid > 0) {
				print '<li class="user"><span>' . l('', 'user') . '</span>' . t('My account') . "</li>\n";
				//print ' &nbsp;|&nbsp; ';
				//print l(t('logout'), 'logout');
				print '<li class="logout"><span>' . l('', 'user/logout') . '</span>' . t('Logout') . "</li>\n";
			} else {
				//print l(t('login'), 'user');
				print '<li class="login"><span>' . l('', 'user') . '</span>' . t('Log in') . "</li>\n";
			}
		?>
	</ul>
      <?php if ($logo): ?>
        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
          <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
        </a>
      <?php endif; ?>

      <?php if ($site_name || $site_slogan): ?>
        <h1>
          <?php if ($site_name): ?>
		<?php print $site_name; ?>
          <?php endif; ?>
          <?php if ($site_slogan): ?>
            <br/><span><?php print $site_slogan; ?></span>
          <?php endif; ?>
        </h1> <!-- /#name-and-slogan -->
      <?php endif; ?>
      <?php print render($page['header']); ?>

    </section></header> <!-- /.section, /#header -->


    <div id="main-wrapper" class="container"><div id="main" class="row clearfix">

	<!--	
	<?php if ($breadcrumb): ?>
		<div id="breadcrumb" class="col-md-12"><?php print $breadcrumb; ?></div>
	<?php endif; ?>
	-->
	
	<?php if ($messages): ?>
		<div class="col-md-12"><?php print $messages; ?></div>
	<?php endif; ?>

	<?php if (drupal_is_front_page()): ?>
	<div id="mainContent" class="column">
        <a id="main-content"></a>
		<section class="row tvt hidden-xs">
		<div class="trouver">
			<div class="lineIcone">
				<div class="line"></div>
				<div class="icone loupe"></div>
			</div>
			<span class="trouver">trouver</span>
			</div>
		<div class="visualiser">
			<div class="lineIcone">
				<div class="line"></div>
				<div class="icone oeil"></div>
			</div>
			<span class="visualiser">visualiser</span>
		</div>
		<div class="telecharger">
			<div class="lineIcone">
				<div class="line"></div>
				<div class="icone telecharger"></div>
			</div>
			<span class="telecharger">télécharger</span>
		</div>
		<p>les cartes et leurs métadonnées de la Région<br/><strong><?php if($site_slogan) print $site_slogan; ?></strong></p>
		</section>
		
		<!-- Search block -->
		<section class="row searchMap">
			<div class="col-sm-6">
				<?php print render($page['search']); ?>
				<p class="searchMore"><span></span><a href="<?php print cartotheque_generate_search_url(); ?>">Recherche avancée</a></p>
			</div>
			<div class="col-sm-6"><?php print render($page['tags']); ?></div>
		</section>
		
		<!-- Maps block -->
		<section class="row actus">
			<div class="col-sm-3"><?php print render($page['home_last_maps']); ?></div>
			<div class="col-sm-6 home-month-maps"><?php print render($page['home_month_maps']); ?></div>
			<div class="col-sm-3"><?php print render($page['home_more_downloaded_maps']); ?></div>
		</section>
      	</div> <!-- /#content -->
		
	<?php else: ?>
	<div id="mainContent" class="container">
	        <a id="main-content"></a>
		<?php if ($page['highlighted']): ?><div id="highlighted"><?php print render($page['highlighted']); ?></div><?php endif; ?>
		<section>
		<?php if(isset($node) && $node->type == "carte" ): ?>
			<div class="backList"><a href="<?php print cartotheque_generate_search_url(); ?>"><span></span>Retour à la recherche</a></div>
			<div class="row map">
			<?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
			<?php print render($page['help']); ?>
			<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
			<?php print render($page['content']); ?>
			<?php print $feed_icons; ?>
			</div>
		<?php else: ?>
			<?php print render($title_prefix); ?>
			<?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
			<?php print render($title_suffix); ?>
			<div <?php if($page['search']): ?> class="col-sm-8" <?php endif; ?>>
			<?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
			<?php print render($page['help']); ?>
			<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
			<?php print render($page['content']); ?>
			<?php print $feed_icons; ?>
			</div>
			<?php if($page['search']): ?>
			<div class="col-sm-4"><div class="sticky-right-bar"><?php print render($page['search']); print render($page['tags']); ?></div></div>
			<?php endif; ?>
		<?php endif; ?>
		</section>
	</div>
	<?php endif; ?>

    </div></div> <!-- /#main, /#main-wrapper -->

    <footer class="container-fluid"><section class="container">
      <?php print render($page['footer']); ?>
    </section></footer> <!-- /.section, /#footer -->

  </div></div> <!-- /#page, /#page-wrapper -->
