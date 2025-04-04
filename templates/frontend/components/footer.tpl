{**
 * templates/frontend/components/footer.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Common site frontend footer.
 *
 * @uses $isFullWidth bool Should this page be displayed without sidebars? This
 *       represents a page-level override, and doesn't indicate whether or not
 *       sidebars have been configured for thesite.
 *}

	</div><!-- pkp_structure_main -->

	{* Sidebars *}
	{if empty($isFullWidth)}
		{capture assign="sidebarCode"}{call_hook name="Templates::Common::Sidebar"}{/capture}
		{if $sidebarCode}
			<div class="pkp_structure_sidebar left" role="complementary" aria-label="{translate|escape key="common.navigation.sidebar"}">
				{$sidebarCode}
			</div><!-- pkp_sidebar.left -->
		{/if}
	{/if}
</div><!-- pkp_structure_content -->

<style>
	.footer {
            background-color: {$darkColor}; /* Darkest color */
            color: white;
            padding: 40px 0;
        }
        .footer h4 {
            color: white;
            font-size: 1.2rem;
            margin-bottom: 20px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
        }
        .footer a {
            color: white;
            text-decoration: none;
        }
        .footer a:hover {
            color: #cccccc;
        }
        .footer p {
            margin-bottom: 0.5rem;
        }
        .social-icons a {
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .contact-info i {
            width: 25px;
        }
        .license-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
</style>

<footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Logo and Description -->
                <div class="col-md-4 mb-4">
                    <h4>Sistema de Revistas Universidad de Nariño</h4>
                    {if $pageFooter}
                        <div>
                            {$pageFooter}
                        </div>
                    {/if}
                </div>

                <!-- Contact Information -->
                <div class="col-md-4 mb-4">
                    <h4>{translate key="plugins.themes.rtend_theme.option.contact"}</h4>
                    <div class="contact-info">
                        {if $authorInformation}
                            <p>{$authorInformation}</p>
                        {/if}
                    </div>
                    <!-- Social Icons -->
                    <div class="social-icons mt-3">
                        {if $socialNavigationMenu}
                            {foreach from=$socialNavigationMenu item=menuItem}
                                <a href="{$menuItem.url|escape}" target="_blank">
                                    {if $menuItem.icon}
                                        <i class="{$menuItem.icon}"></i>
                                    {/if}
                                </a>
                            {/foreach}
                        {/if}
                    </div>
                </div>

                <!-- Important Links -->
                <div class="col-md-4 mb-4">
                    <h4>{translate key="plugins.themes.rtend_theme.option.resources"}</h4>
                    {if $resourcesNavigationMenu}
                            {foreach from=$resourcesNavigationMenu item=menuItem}
                                <p><a href="{$menuItem.url|escape}" target="_blank">
                                    {if $menuItem.icon}
                                        <i class="{$menuItem.icon}"></i> {$menuItem.title|escape}
                                    {/if}
                                </a></p>
                            {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    </footer>

</div><!-- pkp_structure_page -->

{load_script context="frontend"}

{call_hook name="Templates::Common::Footer::PageFooter"}
</body>
</html>
