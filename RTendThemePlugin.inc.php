<?php

/**
 * @file plugins/themes/default/DefaultThemePlugin.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DefaultThemePlugin
 * @ingroup plugins_themes_default
 *
 * @brief Default theme
 */

import('lib.pkp.classes.plugins.ThemePlugin');

class RTendThemePlugin extends ThemePlugin {
	/**
	 * @copydoc ThemePlugin::isActive()
	 */
	public function isActive() {
		if (defined('SESSION_DISABLE_INIT')) return true;
		return parent::isActive();
	}

	/**
	 * Initialize the theme's styles, scripts and hooks. This is run on the
	 * currently active theme and it's parent themes.
	 *
	 * @return null
	 */
	public function init() {
		AppLocale::requireComponents(LOCALE_COMPONENT_PKP_MANAGER, LOCALE_COMPONENT_APP_MANAGER);

		// Register theme options
		$this->addOption('typography', 'FieldOptions', [
			'type' => 'radio',
			'label' => __('plugins.themes.default.option.typography.label'),
			'description' => __('plugins.themes.default.option.typography.description'),
			'options' => [
				[
					'value' => 'notoSans',
					'label' => __('plugins.themes.default.option.typography.notoSans'),
				],
				[
					'value' => 'notoSerif',
					'label' => __('plugins.themes.default.option.typography.notoSerif'),
				],
				[
					'value' => 'notoSerif_notoSans',
					'label' => __('plugins.themes.default.option.typography.notoSerif_notoSans'),
				],
				[
					'value' => 'notoSans_notoSerif',
					'label' => __('plugins.themes.default.option.typography.notoSans_notoSerif'),
				],
				[
					'value' => 'lato',
					'label' => __('plugins.themes.default.option.typography.lato'),
				],
				[
					'value' => 'lora',
					'label' => __('plugins.themes.default.option.typography.lora'),
				],
				[
					'value' => 'lora_openSans',
					'label' => __('plugins.themes.default.option.typography.lora_openSans'),
				],
			],
			'default' => 'notoSans',
		]);

		$this->addOption('baseColour', 'FieldColor', [
			'label' => __('plugins.themes.default.option.colour.label'),
			'description' => __('plugins.themes.default.option.colour.description'),
			'default' => '#1E6292',
		]);

		$this->addOption('showDescriptionInJournalIndex', 'FieldOptions', [
			'label' => __('manager.setup.contextSummary'),
				'options' => [
				[
					'value' => true,
					'label' => __('plugins.themes.default.option.showDescriptionInJournalIndex.option'),
				],
			],
			'default' => false,
		]);
		$this->addOption('useHomepageImageAsHeader', 'FieldOptions', [
			'label' => __('plugins.themes.default.option.useHomepageImageAsHeader.label'),
			'description' => __('plugins.themes.default.option.useHomepageImageAsHeader.description'),
				'options' => [
				[
					'value' => true,
					'label' => __('plugins.themes.default.option.useHomepageImageAsHeader.option')
				],
			],
			'default' => false,
		]);

		// Get base colour (from theme option)
        $themeColor = $this->getOption('baseColour') ?? '#1a4b84';

        // Call the darkenColor method from within the same class
        $darkColor = $this->darkenColor($themeColor, 20); // Darken by 20%

        // Assign to template
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $templateMgr->assign('themeColor', $themeColor);
        $templateMgr->assign('darkColor', $darkColor);

		// Load primary stylesheet
		$this->addStyle('stylesheet', 'styles/index.less');

		// Load custom stylesheet
		$this->addStyle('custom-style', 'styles/custom.css', array('contexts' => 'frontend'));

		// Store additional LESS variables to process based on options
		$additionalLessVariables = array();

		if ($this->getOption('typography') === 'notoSerif') {
			$this->addStyle('font', 'styles/fonts/notoSerif.less');
			$additionalLessVariables[] = '@font: "Noto Serif", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen-Sans", "Ubuntu", "Cantarell", "Helvetica Neue", sans-serif;';
		} elseif (strpos($this->getOption('typography'), 'notoSerif') !== false) {
			$this->addStyle('font', 'styles/fonts/notoSans_notoSerif.less');
			if ($this->getOption('typography') == 'notoSerif_notoSans') {
				$additionalLessVariables[] = '@font-heading: "Noto Serif", serif;';
			} elseif ($this->getOption('typography') == 'notoSans_notoSerif') {
				$additionalLessVariables[] = '@font: "Noto Serif", serif;@font-heading: "Noto Sans", serif;';
			}
		} elseif ($this->getOption('typography') == 'lato') {
			$this->addStyle('font', 'styles/fonts/lato.less');
			$additionalLessVariables[] = '@font: Lato, sans-serif;';
		} elseif ($this->getOption('typography') == 'lora') {
			$this->addStyle('font', 'styles/fonts/lora.less');
			$additionalLessVariables[] = '@font: Lora, serif;';
		} elseif ($this->getOption('typography') == 'lora_openSans') {
			$this->addStyle('font', 'styles/fonts/lora_openSans.less');
			$additionalLessVariables[] = '@font: "Open Sans", sans-serif;@font-heading: Lora, serif;';
		} else {
			$this->addStyle('font', 'styles/fonts/notoSans.less');
		}

		// Update colour based on theme option
		if ($this->getOption('baseColour') !== '#1E6292') {
			$additionalLessVariables[] = '@bg-base:' . $this->getOption('baseColour') . ';';
			if (!$this->isColourDark($this->getOption('baseColour'))) {
				$additionalLessVariables[] = '@text-bg-base:rgba(0,0,0,0.84);';
				$additionalLessVariables[] = '@bg-base-border-color:rgba(0,0,0,0.2);';
			}
		}

		// Pass additional LESS variables based on options
		if (!empty($additionalLessVariables)) {
			$this->modifyStyle('stylesheet', array('addLessVariables' => join("\n", $additionalLessVariables)));
		}

		$request = Application::get()->getRequest();

		// Load icon font FontAwesome - http://fontawesome.io/
		$this->addStyle(
			'fontAwesome',
			$request->getBaseUrl() . '/lib/pkp/styles/fontawesome/fontawesome.css',
			array('baseUrl' => '')
		);

		// Get homepage image and use as header background if useAsHeader is true
		$context = Application::get()->getRequest()->getContext();
		if ($context && $this->getOption('useHomepageImageAsHeader')) {

			$publicFileManager = new PublicFileManager();
			$publicFilesDir = $request->getBaseUrl() . '/' . $publicFileManager->getContextFilesPath($context->getId());

			$homepageImage = $context->getLocalizedData('homepageImage');

			$homepageImageUrl = $publicFilesDir . '/' . $homepageImage['uploadName'];

			$this->addStyle(
				'homepageImage',
				'.pkp_structure_head { background: center / cover no-repeat url("' . $homepageImageUrl . '");}',
				['inline' => true]
			);
		}

		// Load jQuery from a CDN or, if CDNs are disabled, from a local copy.
		$min = Config::getVar('general', 'enable_minified') ? '.min' : '';
		$jquery = $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jquery/jquery' . $min . '.js';
		$jqueryUI = $request->getBaseUrl() . '/lib/pkp/lib/vendor/components/jqueryui/jquery-ui' . $min . '.js';
		// Use an empty `baseUrl` argument to prevent the theme from looking for
		// the files within the theme directory
		$this->addScript('jQuery', $jquery, array('baseUrl' => ''));
		$this->addScript('jQueryUI', $jqueryUI, array('baseUrl' => ''));

		// Load Bootsrap's dropdown
		$this->addScript('popper', 'js/lib/popper/popper.js');
		$this->addScript('bsUtil', 'js/lib/bootstrap/util.js');
		$this->addScript('bsDropdown', 'js/lib/bootstrap/dropdown.js');

		// Load custom JavaScript for this theme
		$this->addScript('default', 'js/main.js');

		// Add navigation menu areas for this theme
		$this->addMenuArea(array('primary', 'user', 'social','resources'));

		HookRegistry::register('TemplateManager::display', array($this, 'loadSocialNavigationMenus'));

		HookRegistry::register('TemplateManager::display', array($this, 'loadResourcesNavigationMenus'));

		$templateMgr = TemplateManager::getManager(Application::get()->getRequest());
    
	}

	private function darkenColor($hex, $percent) {
		// Delete '#' if exists
		$hex = str_replace("#", "", $hex);
	
		// Convert to RGB
		list($r, $g, $b) = sscanf($hex, "%02x%02x%02x");
	
		// Reduce the component based on percentaje
		$r = max(0, min(255, $r - ($r * $percent / 100)));
		$g = max(0, min(255, $g - ($g * $percent / 100)));
		$b = max(0, min(255, $b - ($b * $percent / 100)));
	
		// Return color in Hexadecimal format
		return sprintf("#%02x%02x%02x", $r, $g, $b);
	}

	/**
	 * Load the social navigation menu into the template
	 */
	public function loadSocialNavigationMenus($hookName, $args) {
		$templateMgr = $args[0];
		$request = Application::get()->getRequest();
		$context = $request->getContext();
		
		if (!$context) return false;
		
		$navigationMenuDao = DAORegistry::getDAO('NavigationMenuDAO');
		$navigationMenus = $navigationMenuDao->getByContextId($context->getId());

		$socialNavigationMenu = null;
		while ($navigationMenu = $navigationMenus->next()) {
			if($navigationMenu->getAreaName() == 'social'){
				$socialNavigationMenu = $navigationMenu;
            	break;
			}
		}

		$iconMap = [
			'Facebook' => 'fab fa-facebook-f',
			'Twitter' => 'fa-brands fa-x-twitter',
			'LinkedIn' => 'fab fa-linkedin-in',
			'Email' => 'fas fa-envelope',
			'Instagram' => 'fab fa-instagram',
			'YouTube' => 'fab fa-youtube',
			'TikTok' => 'fab fa-tiktok',
			'Pinterest' => 'fab fa-pinterest-p',
			'Snapchat' => 'fab fa-snapchat-ghost',
			'Reddit' => 'fab fa-reddit-alien',
			'WhatsApp' => 'fab fa-whatsapp',
			'Telegram' => 'fab fa-telegram-plane',
			'Threads' => 'fab fa-threads',
			'Tumblr' => 'fab fa-tumblr',
		];
		
		if ($socialNavigationMenu) {
			$navigationMenuItemDao = DAORegistry::getDAO('NavigationMenuItemDAO');
			$navigationMenuItemAssignmentDao = DAORegistry::getDAO('NavigationMenuItemAssignmentDAO');
			$menuTree = $navigationMenuItemAssignmentDao->getByMenuId($socialNavigationMenu->getId())->toArray();

			// Build the menu tree
			$socialMenu = array();
			foreach ($menuTree as $assignment) {
				$menuItem = $navigationMenuItemDao->getById($assignment->getMenuItemId());
				if ($menuItem) {
					$title = $menuItem->getLocalizedTitle();
        			$url = $menuItem->getLocalizedRemoteUrl();

					 // Try to match by title
					 $iconClass = $iconMap[$title] ?? '';

					 // Fallback: Detect by URL if title is not mapped
					 if (!$iconClass) {
						if (strpos($url, 'facebook.com') !== false) {
							$iconClass = 'fab fa-facebook-f';
						} elseif (strpos($url, 'twitter.com') !== false || strpos($url, 'x.com') !== false) {
							$iconClass = 'fa-brands fa-x-twitter';
						} elseif (strpos($url, 'linkedin.com') !== false) {
							$iconClass = 'fab fa-linkedin-in';
						} elseif (strpos($url, 'instagram.com') !== false) {
							$iconClass = 'fab fa-instagram';
						} elseif (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
							$iconClass = 'fab fa-youtube';
						} elseif (strpos($url, 'tiktok.com') !== false) {
							$iconClass = 'fab fa-tiktok';
						} elseif (strpos($url, 'pinterest.com') !== false) {
							$iconClass = 'fab fa-pinterest-p';
						} elseif (strpos($url, 'snapchat.com') !== false) {
							$iconClass = 'fab fa-snapchat-ghost';
						} elseif (strpos($url, 'reddit.com') !== false) {
							$iconClass = 'fab fa-reddit-alien';
						} elseif (strpos($url, 'whatsapp.com') !== false) {
							$iconClass = 'fab fa-whatsapp';
						} elseif (strpos($url, 'telegram.me') !== false || strpos($url, 't.me') !== false) {
							$iconClass = 'fab fa-telegram-plane';
						} elseif (strpos($url, 'threads.net') !== false) {
							$iconClass = 'fab fa-threads'; // Note: only available in some Font Awesome versions
						} elseif (strpos($url, 'tumblr.com') !== false) {
							$iconClass = 'fab fa-tumblr';
						} elseif (strpos($url, 'mailto:') !== false) {
							$iconClass = 'fas fa-envelope';
						}
					}

					$socialMenu[] = array(
						'title' => $title,
						'url' => $url,
						'icon' => $iconClass,
					);
				}
			}
			
			$templateMgr->assign('socialNavigationMenu', $socialMenu);

			$templateMgr->assign('authorInformation', $context->getSetting('authorInformation',AppLocale::getLocale()));

			
		}
		
		return false;
	}

	/**
	 * Load the resources navigation menu into the template
	 */
	public function loadResourcesNavigationMenus($hookName, $args) {
		$templateMgr = $args[0];
		$request = Application::get()->getRequest();
		$context = $request->getContext();
	
		if (!$context) return false;
	
		$navigationMenuDao = DAORegistry::getDAO('NavigationMenuDAO');
		$navigationMenus = $navigationMenuDao->getByContextId($context->getId());
	
		$resourcesNavigationMenu = null;
		while ($navigationMenu = $navigationMenus->next()) {
			if ($navigationMenu->getAreaName() == 'resources') {
				$resourcesNavigationMenu = $navigationMenu;
				break;
			}
		}
	
		// Iconos por tipo
		$iconMapByType = [
			'NMI_TYPE_USER_REGISTER'   => 'fas fa-user-plus',
			'NMI_TYPE_USER_LOGIN'      => 'fas fa-sign-in-alt',
			'NMI_TYPE_USER_DASHBOARD'  => 'fas fa-tachometer-alt',
			'NMI_TYPE_USER_PROFILE'    => 'fas fa-user',
			'NMI_TYPE_ADMINISTRATION'  => 'fas fa-cogs',
			'NMI_TYPE_USER_LOGOUT'     => 'fas fa-sign-out-alt',
			'NMI_TYPE_CURRENT'         => 'fas fa-link', // Fallback por defecto
			'NMI_TYPE_ANNOUNCEMENTS'   => 'fas fa-bullhorn',
			'NMI_TYPE_ABOUT'           => 'fas fa-info-circle',
			'NMI_TYPE_SUBMISSIONS'     => 'fas fa-paper-plane',
			'NMI_TYPE_EDITORIAL_TEAM'  => 'fas fa-users',
			'NMI_TYPE_PRIVACY'         => 'fas fa-user-shield',
			'NMI_TYPE_CONTACT'         => 'fas fa-envelope',
			'NMI_TYPE_SEARCH'          => 'fas fa-search',
			'NMI_TYPE_ARCHIVES'        => 'fas fa-archive',
			'NMI_TYPE_REMOTE_URL'      => 'fas fa-external-link-alt',
		];
	
		if ($resourcesNavigationMenu) {
			$navigationMenuItemDao = DAORegistry::getDAO('NavigationMenuItemDAO');
			$navigationMenuItemAssignmentDao = DAORegistry::getDAO('NavigationMenuItemAssignmentDAO');
			$menuTree = $navigationMenuItemAssignmentDao->getByMenuId($resourcesNavigationMenu->getId())->toArray();
	
			$resourcesMenu = array();
			foreach ($menuTree as $assignment) {
				$menuItem = $navigationMenuItemDao->getById($assignment->getMenuItemId());
				if ($menuItem) {
					$title = $menuItem->getLocalizedTitle();
					$url = $menuItem->getUrl();
					$type = $menuItem->getType();
	
					$iconClass = $iconMapByType[$type] ?? 'fas fa-link';
	
					// Refinar ícono si es NMI_TYPE_CURRENT
					if ($type === 'NMI_TYPE_CURRENT') {
						$titleKey = $menuItem->getTitleLocaleKey();
						switch ($titleKey) {
							case 'navigation.home':
								$iconClass = 'fas fa-home';
								break;
							case 'navigation.about':
								$iconClass = 'fas fa-info-circle';
								break;
							case 'navigation.archives':
								$iconClass = 'fas fa-archive';
								break;
							default:
								$iconClass = 'fas fa-link';
						}
					}
	
					$resourcesMenu[] = [
						'title' => $title,
						'url' => $url,
						'icon' => $iconClass,
					];
				}
			}
	
			$templateMgr->assign('resourcesNavigationMenu', $resourcesMenu);
		}
	
		return false;
	}

	/**
	 * Get the name of the settings file to be installed on new journal
	 * creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the name of the settings file to be installed site-wide when
	 * OJS is installed.
	 * @return string
	 */
	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.themes.rtend_theme.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription() {
		return __('plugins.themes.rtend_theme.description');
	}
}
