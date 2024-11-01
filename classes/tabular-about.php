<?php

/**
 * tabular about page.
 *
 * @since 0.1.0
 */
class WPGO_Tabular_About_Page {

	protected $_plugin_about_page; // handle to the plugin options page
	protected $_args;

	/**
	 * Plugin options class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'add_about_page' ) );
	}

	/**
	 * Display plugin about page.
	 *
	 * @since 0.1.0
	 */
	public function render_about_page() {
		?>
		<div class="wrap about-wrap">
			<h1>Welcome to Tabular Lite</h1>
			<p style="font-size:1.2em;">Firstly, thank you for choosing Tabular Lite. We've worked very hard to bring you a friendly, easy to use table plugin. We hope you enjoy using it!</p>
			<p style="font-size:1.2em;color:#e14d46;">Note: This initial plugin release is a proof of concept version. Let us know how we can <a style="color:#e14d46;" href="https://wpgoplugins.com/contact-us/" target="_blank">improve</a> the plugin!</p>
			<h2 class="plugin-title">Let's get you started...</h2>
			<h3>Creating a New Table</h3>
			<p style="font-size: 18px;">To create a new table follow these simple steps.</p>
			<ol>
				<li>Click Tabular Lite ><strong><a target="_blank" href="<?php echo get_admin_url() . 'post-new.php?post_type=wpgo_tabular'; ?>">Add New Table</a></strong>.</li>
				<li>Select a table type, then click <strong>Create Table</strong>.</li>
				<li>That's it! <strong>Seriously. It's as simple as that!</strong></li>
			</ol>

			<h3>Table Configuration</h3>
			<p style="font-size: 18px;">So, you've just created a brand new table. Now what?</p>
			<ol>
				<li>After you create a table you'll be presented with the default view for the type of table selected.</li>
				<li>You'll probably want to select your own table style you can do from the <strong>Choose Table Style</strong> meta box.</li>
				<li>Next, edit your table data via the <strong>Edit Table Data</strong> meta box. Tip: You can paste in data directly from a spreadsheet if you prefer rather than entering data manually.</li>
				<li>When you've finished editing your table remember to click <strong>Publish/Update</strong> to save your changes.</li>
			</ol>

			<h3>Displaying Tables</h3>
			<p style="font-size: 18px;">Your table looks amazing, let's share it with the world!</p>
			<ol>
				<li>In the <strong>Table Shortcode</strong> section, copy the shortcode for the table you want to display. It will look something like: <code>[tabular id='123']</code></li>
				<li>Open up a post or page and paste the shortcode (while in Text mode) where you want it to appear.</li>
				<li>Update the post/page and go and view your table on the front end of your site!</li>
				<li>Note: In the near future you'll also be able to enter a table directly from the WordPress editor via a special text editor button.</li>
			</ol>

			<br><hr><br>

			<p style="font-size: 18px;"><a href="http://eepurl.com/c3TqZT" target="_blank">Click here</a> to signup for regular news & updates about the Tabular plugin.</p>

			<p style="font-size: 18px;">Also, <a href="https://wpgoplugins.com/contact-us/" target="_blank">let us know</a> what new tables and features you'd like to see added. We'll do our best to implement them!</p>
		</div><!-- .wrap -->
		<?php
	}

	/**
	 * Register plugin about.
	 *
	 * @since 0.1.0
	 */
	public function add_about_page() {

		add_submenu_page(
			'edit.php?post_type=wpgo_tabular',
			__( 'About Tabular', 'wpgo-tabular' ),
			__( '<span style="margin-right:1px;margin-left:-4px;color:#75b9d4;" class="dashicons dashicons-editor-help"></span>Help & Info', 'wpgo-tabular' ),
			'manage_options',
			'wpgo-tabular-about-page',
			array( &$this, 'render_about_page' )
		);
	}
}