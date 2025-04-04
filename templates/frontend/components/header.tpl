

{**
 * lib/pkp/templates/frontend/components/header.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Common frontend site header.
 *
 * @uses $isFullWidth bool Should this page be displayed without sidebars? This
 *       represents a page-level override, and doesn't indicate whether or not
 *       sidebars have been configured for thesite.
 *}
{strip}
	{* Determine whether a logo or title string is being displayed *}
	{assign var="showingLogo" value=true}
	{if !$displayPageHeaderLogo}
		{assign var="showingLogo" value=false}
	{/if}
{/strip}
<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}" xml:lang="{$currentLocale|replace:"_":"-"}">
{if !$pageTitleTranslated}{capture assign="pageTitleTranslated"}{translate key=$pageTitle}{/capture}{/if}
{include file="frontend/components/headerHead.tpl"}
<body class="pkp_page_{$requestedPage|escape|default:"index"} pkp_op_{$requestedOp|escape|default:"index"}{if $showingLogo} has_site_logo{/if}" dir="{$currentLocaleLangDir|escape|default:"ltr"}">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
       .top-row {
            background-color: {$themeColor}; /* Use the theme color */
            padding: 8px 0;
        }
        .top-row ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .top-row li {
            display: inline-block;
        }
        .top-row a {
            color: white;
            text-decoration: none;
            padding: 0 15px;
        }
        .top-row a:hover {
            opacity: 0.8;
        }
        @media (max-width: 768px) {
            .social-icons, .nav-links {
                text-align: center;
            }
            .nav-links {
                margin-top: 10px;
            }

            #navigationUser{
                text-align: center;
            }

        }

		.navbar {
            background-color: {$darkColor}; /* Darkest color */
            padding: 0.5rem 1rem;
        }
        .navbar-nav .nav-link {
            color: white !important;
            padding: 0.5rem 1rem;
        }
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .dropdown-menu {
            background-color: {$darkColor}; /* Darkest color */
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dropdown-item {
            color: white;
        }
        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .search-button {
            background-color: white;
            color: {$darkColor}; /* Darkest color */
            border: none;
        }
        .search-button:hover {
            background-color: #f8f9fa;
        }
    </style>

	<script src="https://kit.fontawesome.com/7d1bb7724a.js" crossorigin="anonymous"></script>

	<div class="pkp_structure_page">
		{* Header *}
		<header class="" id="" role="banner">
		<div class="top-row">
        <div class="container">
            <div class="row align-items-center">
                {if $socialNavigationMenu}
                    <!-- Social Icons -->
                    <div class="col-md-6 social-icons">
                        <ul>
                            {foreach from=$socialNavigationMenu item=menuItem}
                                <li>
                                    <a href="{$menuItem.url|escape}" target="_blank">
                                        {if $menuItem.icon}
                                            <i class="{$menuItem.icon}"></i>
                                        {/if}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}

                <!-- Right Side Links -->
                <div class="col-md-6 text-end nav-links">
					{load_menu name="user" id="navigationUser" ulClass="" liClass=""}
                </div>
            </div>
        </div>
    </div>
    </div>

<div class="top-row  d-flex justify-content-center">
{if $displayPageHeaderLogo}
						<a href="{$homeUrl}" class="is_img">
							<img src="{$publicFilesDir}/{$displayPageHeaderLogo.uploadName|escape:"url"}" width="{$displayPageHeaderLogo.width|escape}" height="{$displayPageHeaderLogo.height|escape}" {if $displayPageHeaderLogo.altText != ''}alt="{$displayPageHeaderLogo.altText|escape}"{/if} />
						</a>
					{elseif $displayPageHeaderTitle}
						<a href="{$homeUrl}" class="is_text">{$displayPageHeaderTitle|escape}</a>
					{else}
						<a href="{$homeUrl}" class="is_img">
							<img src="{$baseUrl}/templates/images/structure/logo.png" alt="{$applicationName|escape}" title="{$applicationName|escape}" width="180" height="90" />
						</a>
					{/if}
</div>

	<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- Toggle Button for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#mainNavbar" aria-controls="mainNavbar" 
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                {capture assign="primaryMenu"}
					{load_menu name="primary" id="navigationPrimary" ulClass="pkp_navigation_primary"}
				{/capture}

				{$primaryMenu}

                {* Search form *}
				{if $currentContext && $requestedPage !== 'search'}
					<div class="pkp_navigation_search_wrapper">
						<a href="{url page="search"}" class="pkp_search pkp_search_desktop">
							<span class="fa fa-search" aria-hidden="true"></span>
							{translate key="common.search"}
						</a>
					</div>
				{/if}
            </div>
        </div>
    </nav>
		</header><!-- .pkp_structure_head -->

		{* Wrapper for page content and sidebars *}
		{if $isFullWidth}
			{assign var=hasSidebar value=0}
		{/if}
		<div class="pkp_structure_content{if $hasSidebar} has_sidebar{/if}">
			<div class="pkp_structure_main" role="main">
				<a id="pkp_content_main"></a>
