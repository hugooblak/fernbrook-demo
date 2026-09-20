<?php
/**
 * The AI Search menu: an overview, the business profile, and the crawler policy.
 *
 * The other screens (Redirects, Validate, Headings and links) live next to the code that
 * does their work.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'AI Search', 'fernbrook-aeo' ),
			__( 'AI Search', 'fernbrook-aeo' ),
			'manage_options',
			'fernbrook-ai-search',
			'fbaeo_screen_overview',
			'dashicons-search',
			5
		);
		$pages = array(
			'fernbrook-ai-search'           => array( __( 'Overview', 'fernbrook-aeo' ), 'fbaeo_screen_overview' ),
			'fernbrook-ai-search-profile'   => array( __( 'Business profile', 'fernbrook-aeo' ), 'fbaeo_screen_profile' ),
			'fernbrook-ai-search-validate'  => array( __( 'Validate', 'fernbrook-aeo' ), 'fbaeo_screen_validate' ),
			'fernbrook-ai-search-audit'     => array( __( 'Headings and links', 'fernbrook-aeo' ), 'fbaeo_screen_audit' ),
			'fernbrook-ai-search-crawlers'  => array( __( 'Crawlers and llms.txt', 'fernbrook-aeo' ), 'fbaeo_screen_crawlers' ),
			'fernbrook-ai-search-redirects' => array( __( 'Redirects', 'fernbrook-aeo' ), 'fbaeo_screen_redirects' ),
		);
		foreach ( $pages as $slug => $page ) {
			add_submenu_page( 'fernbrook-ai-search', $page[0], $page[0], 'manage_options', $slug, $page[1] );
		}
	}
);

/**
 * Overview: what this plugin is doing, and where to go next.
 */
function fbaeo_screen_overview() {
	$content = fbaeo_all_content();
	$fails   = 0;
	foreach ( $content as $post ) {
		foreach ( fbaeo_check_post( $post ) as $result ) {
			if ( 'fail' === $result['state'] ) {
				$fails++;
			}
		}
	}
	$settings = fbaeo_robots_settings();
	$blocked  = 0;
	foreach ( fbaeo_crawlers() as $name => $info ) {
		if ( empty( $settings['allow'][ $name ] ) ) {
			$blocked++;
		}
	}
	?>
	<div class="wrap fbaeo-wrap">
		<h1><?php esc_html_e( 'AI Search', 'fernbrook-aeo' ); ?></h1>
		<p class="fbaeo-intro"><?php esc_html_e( 'This plugin writes everything a search engine or an AI assistant reads about this business: one connected JSON-LD graph per page, the metadata, the canonical links, the crawler policy and llms.txt. It is a plugin and not theme code on purpose, so none of it is lost in a redesign.', 'fernbrook-aeo' ); ?></p>

		<div class="fbaeo-grid">
			<div class="fbaeo-card">
				<p class="fbaeo-num <?php echo $fails ? 'fbaeo-fail' : 'fbaeo-pass'; ?>"><?php echo (int) $fails; ?></p>
				<h3><?php esc_html_e( 'Checks failing', 'fernbrook-aeo' ); ?></h3>
				<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=fernbrook-ai-search-validate' ) ); ?>"><?php esc_html_e( 'Open Validate', 'fernbrook-aeo' ); ?></a></p>
			</div>
			<div class="fbaeo-card">
				<p class="fbaeo-num"><?php echo (int) count( $content ); ?></p>
				<h3><?php esc_html_e( 'Pages with structured data', 'fernbrook-aeo' ); ?></h3>
				<p><?php esc_html_e( 'Every published page and guide.', 'fernbrook-aeo' ); ?></p>
			</div>
			<div class="fbaeo-card">
				<p class="fbaeo-num"><?php echo (int) $blocked; ?></p>
				<h3><?php esc_html_e( 'AI crawlers blocked', 'fernbrook-aeo' ); ?></h3>
				<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=fernbrook-ai-search-crawlers' ) ); ?>"><?php esc_html_e( 'Open the crawler policy', 'fernbrook-aeo' ); ?></a></p>
			</div>
			<div class="fbaeo-card">
				<p class="fbaeo-num"><?php echo (int) count( fbaeo_redirects() ); ?></p>
				<h3><?php esc_html_e( 'Redirects in place', 'fernbrook-aeo' ); ?></h3>
				<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=fernbrook-ai-search-redirects' ) ); ?>"><?php esc_html_e( 'Open Redirects', 'fernbrook-aeo' ); ?></a></p>
			</div>
		</div>

		<h2><?php esc_html_e( 'How the entity graph is put together', 'fernbrook-aeo' ); ?></h2>
		<p class="fbaeo-intro"><?php esc_html_e( 'Business entities live at the site root, so renaming a page never breaks them. Page entities live at the page address. Everything points at everything else by @id.', 'fernbrook-aeo' ); ?></p>
		<table class="widefat striped fbaeo-table">
			<thead><tr><th style="width:24rem"><?php esc_html_e( 'Stable @id', 'fernbrook-aeo' ); ?></th><th><?php esc_html_e( 'What it is', 'fernbrook-aeo' ); ?></th></tr></thead>
			<tbody>
			<?php
			$examples = array(
				array( fbaeo_id( 'organization' ), __( 'The company as a legal entity. Publisher of everything.', 'fernbrook-aeo' ) ),
				array( fbaeo_id( 'localbusiness' ), __( 'The place people visit: address, hours, phone, areas served.', 'fernbrook-aeo' ) ),
				array( fbaeo_id( 'website' ), __( 'The website itself, published by the organization.', 'fernbrook-aeo' ) ),
				array( fbaeo_id( 'person-dana-whitlock' ), __( 'A trainer. The same node is the author of every guide she writes.', 'fernbrook-aeo' ) ),
				array( fbaeo_id( 'service-puppy-foundations' ), __( 'The service the business sells.', 'fernbrook-aeo' ) ),
				array( fbaeo_id( 'course-puppy-foundations' ), __( 'The taught course, with its dates as CourseInstances.', 'fernbrook-aeo' ) ),
				array( fbaeo_id( 'place-venue' ), __( 'The hall. Every class Event points at this one Place.', 'fernbrook-aeo' ) ),
				array( home_url( '/about/' ) . '#webpage', __( 'One page. Page-level nodes hang off the page address.', 'fernbrook-aeo' ) ),
			);
			foreach ( $examples as $example ) :
				?>
				<tr><td><code><?php echo esc_html( $example[0] ); ?></code></td><td><?php echo esc_html( $example[1] ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'Files this plugin serves', 'fernbrook-aeo' ); ?></h2>
		<ul style="list-style:disc;margin-left:1.4rem">
			<li><a href="<?php echo esc_url( home_url( '/robots.txt' ) ); ?>" target="_blank" rel="noopener"><code>/robots.txt</code></a> — <?php esc_html_e( 'the crawler policy, including one rule per AI crawler.', 'fernbrook-aeo' ); ?></li>
			<li><a href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>" target="_blank" rel="noopener"><code>/llms.txt</code></a> — <?php esc_html_e( 'a plain-text index of the key pages, generated from the pages themselves.', 'fernbrook-aeo' ); ?></li>
			<li><a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>" target="_blank" rel="noopener"><code>/wp-sitemap.xml</code></a> — <?php esc_html_e( "WordPress's own sitemap, left switched on and linked from robots.txt.", 'fernbrook-aeo' ); ?></li>
		</ul>

		<h2><?php esc_html_e( 'Search Console', 'fernbrook-aeo' ); ?></h2>
		<p class="fbaeo-intro"><?php esc_html_e( 'Paste the verification code from Search Console into the business profile and it is printed in the head of every page. Search Console then needs a few days of its own data before it can show anything — no plugin can shortcut that.', 'fernbrook-aeo' ); ?></p>
	</div>
	<?php
}

/**
 * Business profile screen.
 */
function fbaeo_screen_profile() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to see this screen.', 'fernbrook-aeo' ) );
	}
	$notice = '';
	if ( isset( $_POST['fbaeo_profile_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbaeo_profile_nonce'] ) ), 'fbaeo_profile' ) ) {
		$profile = fbaeo_profile();
		$simple  = array( 'legal_name', 'name', 'business_type', 'additional_type', 'description', 'founding_date', 'phone', 'phone_display', 'email', 'street', 'locality', 'region', 'postal_code', 'country', 'latitude', 'longitude', 'price_range', 'currency', 'gsc_token', 'venue_name' );
		foreach ( $simple as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$profile[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
			}
		}
		foreach ( array( 'areas', 'same_as', 'knows_about' ) as $key ) {
			$raw             = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
			$profile[ $key ] = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', $raw ) ) ) );
		}
		$hours_raw = isset( $_POST['hours'] ) ? sanitize_textarea_field( wp_unslash( $_POST['hours'] ) ) : '';
		$hours     = array();
		foreach ( array_filter( array_map( 'trim', preg_split( '/\R/', $hours_raw ) ) ) as $line ) {
			if ( ! preg_match( '/^(.+)\s+(\d{2}:\d{2})-(\d{2}:\d{2})$/', $line, $m ) ) {
				continue;
			}
			$hours[] = array(
				'days'   => array_values( array_filter( array_map( 'trim', explode( ',', $m[1] ) ) ) ),
				'opens'  => $m[2],
				'closes' => $m[3],
			);
		}
		if ( $hours ) {
			$profile['hours'] = $hours;
		}
		$people  = array();
		$names   = isset( $_POST['person_name'] ) ? (array) wp_unslash( $_POST['person_name'] ) : array();
		$default = isset( $_POST['person_default'] ) ? sanitize_title( wp_unslash( $_POST['person_default'] ) ) : '';
		foreach ( $names as $i => $name ) {
			$name = sanitize_text_field( $name );
			if ( ! $name ) {
				continue;
			}
			$slug     = sanitize_title( isset( $_POST['person_slug'][ $i ] ) ? wp_unslash( $_POST['person_slug'][ $i ] ) : $name );
			$people[] = array(
				'slug'      => $slug,
				'name'      => $name,
				'job'       => sanitize_text_field( isset( $_POST['person_job'][ $i ] ) ? wp_unslash( $_POST['person_job'][ $i ] ) : '' ),
				'bio'       => sanitize_textarea_field( isset( $_POST['person_bio'][ $i ] ) ? wp_unslash( $_POST['person_bio'][ $i ] ) : '' ),
				'same_as'   => array_values( array_filter( array_map( 'trim', preg_split( '/\R/', sanitize_textarea_field( isset( $_POST['person_same_as'][ $i ] ) ? wp_unslash( $_POST['person_same_as'][ $i ] ) : '' ) ) ) ) ),
				'is_author' => $default === $slug ? '1' : '',
			);
		}
		if ( $people ) {
			$profile['people'] = $people;
		}
		update_option( FBAEO_OPTION, $profile );
		$notice = __( 'Business profile saved. Every page picks it up immediately.', 'fernbrook-aeo' );
	}

	$p     = fbaeo_profile();
	$hours = array();
	foreach ( $p['hours'] as $slot ) {
		$hours[] = implode( ',', $slot['days'] ) . ' ' . $slot['opens'] . '-' . $slot['closes'];
	}
	$text = function ( $key, $label, $help = '', $size = 'regular-text' ) use ( $p ) {
		printf(
			'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input type="text" class="%4$s" id="%1$s" name="%1$s" value="%3$s">%5$s</td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( is_array( $p[ $key ] ) ? '' : $p[ $key ] ),
			esc_attr( $size ),
			$help ? '<p class="description">' . esc_html( $help ) . '</p>' : ''
		);
	};
	?>
	<div class="wrap fbaeo-wrap">
		<h1><?php esc_html_e( 'Business profile', 'fernbrook-aeo' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>
		<p class="fbaeo-intro"><?php esc_html_e( 'Type the business details once. The entity graph, the Open Graph tags and llms.txt all read from here, so a changed phone number is one edit, not nine.', 'fernbrook-aeo' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'fbaeo_profile', 'fbaeo_profile_nonce' ); ?>

			<h2><?php esc_html_e( 'Who the business is', 'fernbrook-aeo' ); ?></h2>
			<table class="form-table"><tbody>
				<?php
				$text( 'legal_name', __( 'Legal name', 'fernbrook-aeo' ), __( 'Used for the Organization node.', 'fernbrook-aeo' ) );
				$text( 'name', __( 'Trading name', 'fernbrook-aeo' ), __( 'What people call it. Used for the LocalBusiness node.', 'fernbrook-aeo' ) );
				$text( 'business_type', __( 'schema.org type', 'fernbrook-aeo' ), __( 'schema.org has no type for a dog trainer, so this stays LocalBusiness and the specific meaning comes from the line below. Inventing a type nothing recognises helps nobody.', 'fernbrook-aeo' ) );
				$text( 'additional_type', __( 'additionalType', 'fernbrook-aeo' ), __( 'A public address that identifies the trade, usually a Wikipedia page. This is how you say "dog training" in a way a machine can look up.', 'fernbrook-aeo' ), 'large-text' );
				?>
				<tr><th scope="row"><label for="description"><?php esc_html_e( 'One-paragraph description', 'fernbrook-aeo' ); ?></label></th>
					<td><textarea id="description" name="description" rows="3" class="large-text"><?php echo esc_textarea( $p['description'] ); ?></textarea></td></tr>
				<?php $text( 'founding_date', __( 'Year founded', 'fernbrook-aeo' ) ); ?>
			</tbody></table>

			<h2><?php esc_html_e( 'Where and when', 'fernbrook-aeo' ); ?></h2>
			<table class="form-table"><tbody>
				<?php
				$text( 'street', __( 'Street', 'fernbrook-aeo' ) );
				$text( 'locality', __( 'Town', 'fernbrook-aeo' ) );
				$text( 'region', __( 'State', 'fernbrook-aeo' ) );
				$text( 'postal_code', __( 'ZIP code', 'fernbrook-aeo' ) );
				$text( 'country', __( 'Country code', 'fernbrook-aeo' ) );
				$text( 'latitude', __( 'Latitude', 'fernbrook-aeo' ), __( 'Rounded sample values on this demo site, because the town is invented. On a real site, take them from the business\'s own Google Business Profile pin.', 'fernbrook-aeo' ) );
				$text( 'longitude', __( 'Longitude', 'fernbrook-aeo' ) );
				$text( 'phone', __( 'Phone (international format)', 'fernbrook-aeo' ) );
				$text( 'phone_display', __( 'Phone as written on the site', 'fernbrook-aeo' ) );
				$text( 'email', __( 'Email', 'fernbrook-aeo' ) );
				$text( 'venue_name', __( 'Venue name', 'fernbrook-aeo' ), __( 'Where classes happen. Every class Event points at this one Place.', 'fernbrook-aeo' ) );
				$text( 'price_range', __( 'Price range', 'fernbrook-aeo' ) );
				$text( 'currency', __( 'Currency code', 'fernbrook-aeo' ) );
				?>
				<tr><th scope="row"><label for="hours"><?php esc_html_e( 'Opening hours', 'fernbrook-aeo' ); ?></label></th>
					<td><textarea id="hours" name="hours" rows="3" class="large-text"><?php echo esc_textarea( implode( "\n", $hours ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One line per block of days: day names separated by commas, then a space, then 08:00-18:00.', 'fernbrook-aeo' ); ?></p></td></tr>
				<tr><th scope="row"><label for="areas"><?php esc_html_e( 'Areas served', 'fernbrook-aeo' ); ?></label></th>
					<td><textarea id="areas" name="areas" rows="4" class="large-text"><?php echo esc_textarea( implode( "\n", $p['areas'] ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One town per line.', 'fernbrook-aeo' ); ?></p></td></tr>
			</tbody></table>

			<h2><?php esc_html_e( 'How the business is identified elsewhere', 'fernbrook-aeo' ); ?></h2>
			<table class="form-table"><tbody>
				<tr><th scope="row"><label for="same_as"><?php esc_html_e( 'sameAs addresses', 'fernbrook-aeo' ); ?></label></th>
					<td><textarea id="same_as" name="same_as" rows="4" class="large-text"><?php echo esc_textarea( implode( "\n", $p['same_as'] ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Your Google Business Profile, your social accounts, your listing on any trade body. One per line. This is what ties this website to the same business elsewhere on the web.', 'fernbrook-aeo' ); ?></p></td></tr>
				<tr><th scope="row"><label for="knows_about"><?php esc_html_e( 'Subjects the business knows about', 'fernbrook-aeo' ); ?></label></th>
					<td><textarea id="knows_about" name="knows_about" rows="4" class="large-text"><?php echo esc_textarea( implode( "\n", $p['knows_about'] ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'One per line. Keep it to what the site actually covers.', 'fernbrook-aeo' ); ?></p></td></tr>
				<?php $text( 'gsc_token', __( 'Search Console verification code', 'fernbrook-aeo' ), __( 'The content value from the HTML tag method. Printed in the head of every page.', 'fernbrook-aeo' ), 'large-text' ); ?>
			</tbody></table>

			<h2><?php esc_html_e( 'People', 'fernbrook-aeo' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Each person becomes one Person node with a stable @id. The same node is the author of every guide they write, so three articles by one trainer are three articles by one person, not by three.', 'fernbrook-aeo' ); ?></p>
			<?php foreach ( array_pad( $p['people'], max( 2, count( $p['people'] ) + 1 ), array( 'slug' => '', 'name' => '', 'job' => '', 'bio' => '', 'same_as' => array(), 'is_author' => '' ) ) as $i => $person ) : ?>
				<table class="form-table"><tbody>
					<tr><th scope="row"><?php esc_html_e( 'Name', 'fernbrook-aeo' ); ?></th>
						<td><input type="text" class="regular-text" name="person_name[]" value="<?php echo esc_attr( $person['name'] ); ?>">
						<input type="text" class="regular-text" name="person_slug[]" value="<?php echo esc_attr( $person['slug'] ); ?>" placeholder="<?php esc_attr_e( 'slug for the @id', 'fernbrook-aeo' ); ?>">
						<label style="margin-left:.6rem"><input type="radio" name="person_default" value="<?php echo esc_attr( $person['slug'] ); ?>" <?php checked( ! empty( $person['is_author'] ) ); ?>> <?php esc_html_e( 'Default author', 'fernbrook-aeo' ); ?></label></td></tr>
					<tr><th scope="row"><?php esc_html_e( 'Job title', 'fernbrook-aeo' ); ?></th>
						<td><input type="text" class="large-text" name="person_job[]" value="<?php echo esc_attr( $person['job'] ); ?>"></td></tr>
					<tr><th scope="row"><?php esc_html_e( 'Short biography', 'fernbrook-aeo' ); ?></th>
						<td><textarea name="person_bio[]" rows="2" class="large-text"><?php echo esc_textarea( $person['bio'] ); ?></textarea></td></tr>
					<tr><th scope="row"><?php esc_html_e( 'sameAs addresses', 'fernbrook-aeo' ); ?></th>
						<td><textarea name="person_same_as[]" rows="2" class="large-text"><?php echo esc_textarea( implode( "\n", (array) $person['same_as'] ) ); ?></textarea></td></tr>
				</tbody></table>
				<hr>
			<?php endforeach; ?>

			<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Save the business profile', 'fernbrook-aeo' ); ?></button></p>
		</form>
	</div>
	<?php
}

/**
 * Crawlers and llms.txt screen.
 */
function fbaeo_screen_crawlers() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to see this screen.', 'fernbrook-aeo' ) );
	}
	$notice = '';
	if ( isset( $_POST['fbaeo_robots_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbaeo_robots_nonce'] ) ), 'fbaeo_robots' ) ) {
		$allow = array();
		foreach ( fbaeo_crawlers() as $name => $info ) {
			$allow[ $name ] = isset( $_POST['allow'][ $name ] ) ? '1' : '';
		}
		update_option(
			FBAEO_ROBOTS_OPTION,
			array(
				'allow' => $allow,
				'extra' => isset( $_POST['extra'] ) ? sanitize_textarea_field( wp_unslash( $_POST['extra'] ) ) : '',
			)
		);
		$notice = __( 'Crawler policy saved and written into robots.txt.', 'fernbrook-aeo' );
	}
	$settings = fbaeo_robots_settings();
	?>
	<div class="wrap fbaeo-wrap">
		<h1><?php esc_html_e( 'Crawlers and llms.txt', 'fernbrook-aeo' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>
		<p class="fbaeo-intro"><?php esc_html_e( 'Which AI crawlers may read this site. This is a business decision, not a technical one: some of these send visitors and cite you, some only take the content. Each row says which is which. Blocking a crawler here stops well-behaved ones; it does not stop anyone determined.', 'fernbrook-aeo' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'fbaeo_robots', 'fbaeo_robots_nonce' ); ?>
			<table class="widefat striped fbaeo-table">
				<thead><tr><th style="width:5rem"><?php esc_html_e( 'Allow', 'fernbrook-aeo' ); ?></th><th style="width:14rem"><?php esc_html_e( 'Crawler', 'fernbrook-aeo' ); ?></th><th><?php esc_html_e( 'What it does', 'fernbrook-aeo' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( fbaeo_crawlers() as $name => $info ) : ?>
					<tr>
						<td><input type="checkbox" name="allow[<?php echo esc_attr( $name ); ?>]" value="1" <?php checked( ! empty( $settings['allow'][ $name ] ) ); ?>></td>
						<td><code><?php echo esc_html( $name ); ?></code></td>
						<td><p class="fbaeo-crawler-note"><?php echo esc_html( $info[0] ); ?></p></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<h2><?php esc_html_e( 'Extra robots.txt rules', 'fernbrook-aeo' ); ?></h2>
			<textarea name="extra" rows="4" class="large-text"><?php echo esc_textarea( $settings['extra'] ); ?></textarea>
			<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Save the crawler policy', 'fernbrook-aeo' ); ?></button></p>
		</form>

		<h2><?php esc_html_e( 'What robots.txt says now', 'fernbrook-aeo' ); ?></h2>
		<pre class="fbaeo-json"><?php echo esc_html( fbaeo_robots_block() ); ?></pre>

		<h2><?php esc_html_e( 'What llms.txt says now', 'fernbrook-aeo' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Generated from the pages ticked as "key page" in their AI search panel, plus every guide. Nothing to keep up to date by hand.', 'fernbrook-aeo' ); ?></p>
		<pre class="fbaeo-json"><?php echo esc_html( fbaeo_llms_text() ); ?></pre>
	</div>
	<?php
}
