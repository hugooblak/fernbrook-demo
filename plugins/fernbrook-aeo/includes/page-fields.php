<?php
/**
 * The "AI search" panel on every page and post.
 *
 * Three things live here: the words search engines and assistants read (title, description),
 * the indexing rules (canonical, noindex), and which schema.org type this page is.
 * Nothing here is guessed at render time — what the editor sets is what gets published.
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

/**
 * The schema types an editor can pick, and what each one is for.
 *
 * @return array
 */
function fbaeo_types() {
	return array(
		''           => __( 'Page (default)', 'fernbrook-aeo' ),
		'service'    => __( 'Service — something the business does for a customer', 'fernbrook-aeo' ),
		'course'     => __( 'Course — a taught program with a syllabus and a price', 'fernbrook-aeo' ),
		'article'    => __( 'Article — a written guide with an author and a date', 'fernbrook-aeo' ),
		'faq'        => __( 'FAQ page — questions and answers, nothing else', 'fernbrook-aeo' ),
		'contact'    => __( 'Contact page', 'fernbrook-aeo' ),
		'about'      => __( 'About page', 'fernbrook-aeo' ),
		'collection' => __( 'Collection — a list of other pages', 'fernbrook-aeo' ),
	);
}

/**
 * Every field the panel saves, with its type. One list, used by the form and the save handler.
 *
 * @return array
 */
function fbaeo_fields() {
	return array(
		'_fbaeo_type'        => 'key',
		'_fbaeo_entity'      => 'slug',
		'_fbaeo_title'       => 'text',
		'_fbaeo_description' => 'textarea',
		'_fbaeo_canonical'   => 'url',
		'_fbaeo_noindex'     => 'bool',
		'_fbaeo_primary'     => 'bool',
		'_fbaeo_price'       => 'text',
		'_fbaeo_weeks'       => 'int',
		'_fbaeo_audience'    => 'text',
		'_fbaeo_author'      => 'slug',
		'_fbaeo_video_name'  => 'text',
		'_fbaeo_video_desc'  => 'textarea',
		'_fbaeo_video_url'   => 'url',
		'_fbaeo_video_thumb' => 'url',
		'_fbaeo_video_date'  => 'text',
	);
}

/**
 * Read one field, cleaned.
 *
 * @param int    $post_id Post.
 * @param string $key     Meta key.
 * @return string
 */
function fbaeo_meta( $post_id, $key ) {
	return (string) get_post_meta( $post_id, $key, true );
}

add_action(
	'add_meta_boxes',
	function () {
		foreach ( array( 'page', 'post' ) as $type ) {
			add_meta_box( 'fbaeo_panel', __( 'AI search: schema, metadata and indexing', 'fernbrook-aeo' ), 'fbaeo_render_panel', $type, 'normal', 'default' );
		}
	}
);

/**
 * The panel itself.
 *
 * @param WP_Post $post Post being edited.
 */
function fbaeo_render_panel( $post ) {
	wp_nonce_field( 'fbaeo_save', 'fbaeo_nonce' );
	$type   = fbaeo_meta( $post->ID, '_fbaeo_type' );
	$entity = fbaeo_meta( $post->ID, '_fbaeo_entity' );
	$people = fbaeo_get( 'people', array() );
	?>
	<div class="fbaeo-meta-panel">
		<p class="description">
			<?php esc_html_e( 'Every field here ends up in the page\'s head or in its JSON-LD. Leave a field empty and the plugin falls back to the page title and excerpt, and says so on the Validate screen.', 'fernbrook-aeo' ); ?>
		</p>

		<h4><?php esc_html_e( 'What this page is', 'fernbrook-aeo' ); ?></h4>
		<p>
			<label for="fbaeo_type"><strong><?php esc_html_e( 'Schema type', 'fernbrook-aeo' ); ?></strong></label><br>
			<select name="_fbaeo_type" id="fbaeo_type" style="min-width:32rem;max-width:100%">
				<?php foreach ( fbaeo_types() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $type, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="fbaeo_entity"><strong><?php esc_html_e( 'Entity name for the @id', 'fernbrook-aeo' ); ?></strong></label><br>
			<input type="text" name="_fbaeo_entity" id="fbaeo_entity" class="regular-text" value="<?php echo esc_attr( $entity ); ?>" placeholder="<?php echo esc_attr( $post->post_name ); ?>">
			<span class="description"><?php esc_html_e( 'Leave empty to use the page slug. Changing this changes the @id, so only change it on purpose.', 'fernbrook-aeo' ); ?></span>
		</p>

		<h4><?php esc_html_e( 'What search engines and assistants read', 'fernbrook-aeo' ); ?></h4>
		<p>
			<label for="fbaeo_title"><strong><?php esc_html_e( 'Title tag', 'fernbrook-aeo' ); ?></strong></label><br>
			<input type="text" name="_fbaeo_title" id="fbaeo_title" class="large-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_title' ) ); ?>">
			<span class="description"><?php esc_html_e( 'Aim for 15 to 60 characters. The Validate screen counts them for you.', 'fernbrook-aeo' ); ?></span>
		</p>
		<p>
			<label for="fbaeo_description"><strong><?php esc_html_e( 'Meta description', 'fernbrook-aeo' ); ?></strong></label><br>
			<textarea name="_fbaeo_description" id="fbaeo_description" class="large-text" rows="3"><?php echo esc_textarea( fbaeo_meta( $post->ID, '_fbaeo_description' ) ); ?></textarea>
			<span class="description"><?php esc_html_e( 'Aim for 70 to 160 characters. Answer the question first, then say who you are.', 'fernbrook-aeo' ); ?></span>
		</p>

		<h4><?php esc_html_e( 'Indexing', 'fernbrook-aeo' ); ?></h4>
		<p>
			<label for="fbaeo_canonical"><strong><?php esc_html_e( 'Canonical address', 'fernbrook-aeo' ); ?></strong></label><br>
			<input type="url" name="_fbaeo_canonical" id="fbaeo_canonical" class="large-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_canonical' ) ); ?>" placeholder="<?php echo esc_attr( get_permalink( $post ) ); ?>">
			<span class="description"><?php esc_html_e( 'Leave empty unless this page duplicates another one. Empty means the page points at itself.', 'fernbrook-aeo' ); ?></span>
		</p>
		<p>
			<label><input type="checkbox" name="_fbaeo_noindex" value="1" <?php checked( fbaeo_meta( $post->ID, '_fbaeo_noindex' ), '1' ); ?>> <?php esc_html_e( 'Keep this page out of search results (noindex)', 'fernbrook-aeo' ); ?></label><br>
			<label><input type="checkbox" name="_fbaeo_primary" value="1" <?php checked( fbaeo_meta( $post->ID, '_fbaeo_primary' ), '1' ); ?>> <?php esc_html_e( 'List this page in llms.txt as a key page', 'fernbrook-aeo' ); ?></label>
		</p>

		<h4><?php esc_html_e( 'Extra facts for Service and Course pages', 'fernbrook-aeo' ); ?></h4>
		<p>
			<label for="fbaeo_price"><?php esc_html_e( 'Price', 'fernbrook-aeo' ); ?></label>
			<input type="text" name="_fbaeo_price" id="fbaeo_price" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_price' ) ); ?>" placeholder="320" size="8">
			&nbsp;
			<label for="fbaeo_weeks"><?php esc_html_e( 'Weeks', 'fernbrook-aeo' ); ?></label>
			<input type="number" name="_fbaeo_weeks" id="fbaeo_weeks" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_weeks' ) ); ?>" min="0" max="52" size="4">
			&nbsp;
			<label for="fbaeo_audience"><?php esc_html_e( 'Who it is for', 'fernbrook-aeo' ); ?></label>
			<input type="text" name="_fbaeo_audience" id="fbaeo_audience" class="regular-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_audience' ) ); ?>" placeholder="<?php esc_attr_e( 'Puppies 8 to 18 weeks', 'fernbrook-aeo' ); ?>">
		</p>

		<h4><?php esc_html_e( 'Author (Article pages)', 'fernbrook-aeo' ); ?></h4>
		<p>
			<select name="_fbaeo_author">
				<option value=""><?php esc_html_e( 'Use the default author from the business profile', 'fernbrook-aeo' ); ?></option>
				<?php foreach ( $people as $person ) : ?>
					<option value="<?php echo esc_attr( $person['slug'] ); ?>" <?php selected( fbaeo_meta( $post->ID, '_fbaeo_author' ), $person['slug'] ); ?>><?php echo esc_html( $person['name'] ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="description"><?php esc_html_e( 'The author is linked by @id to the Person node, so the same trainer on three articles is one entity, not three.', 'fernbrook-aeo' ); ?></span>
		</p>

		<h4><?php esc_html_e( 'Video (optional)', 'fernbrook-aeo' ); ?></h4>
		<p class="description"><?php esc_html_e( 'Fill all four and the page gets VideoObject data. Leave them empty and no video data is written — an empty VideoObject is worse than none.', 'fernbrook-aeo' ); ?></p>
		<p>
			<input type="text" name="_fbaeo_video_name" class="regular-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_video_name' ) ); ?>" placeholder="<?php esc_attr_e( 'Video title', 'fernbrook-aeo' ); ?>">
			<input type="url" name="_fbaeo_video_url" class="regular-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_video_url' ) ); ?>" placeholder="<?php esc_attr_e( 'Address of the video page', 'fernbrook-aeo' ); ?>"><br>
			<input type="url" name="_fbaeo_video_thumb" class="regular-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_video_thumb' ) ); ?>" placeholder="<?php esc_attr_e( 'Thumbnail image address', 'fernbrook-aeo' ); ?>">
			<input type="text" name="_fbaeo_video_date" class="regular-text" value="<?php echo esc_attr( fbaeo_meta( $post->ID, '_fbaeo_video_date' ) ); ?>" placeholder="<?php esc_attr_e( 'Upload date, e.g. 2026-03-01', 'fernbrook-aeo' ); ?>"><br>
			<textarea name="_fbaeo_video_desc" class="large-text" rows="2" placeholder="<?php esc_attr_e( 'What the video shows', 'fernbrook-aeo' ); ?>"><?php echo esc_textarea( fbaeo_meta( $post->ID, '_fbaeo_video_desc' ) ); ?></textarea>
		</p>
	</div>
	<?php
}

add_action(
	'save_post',
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['fbaeo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbaeo_nonce'] ) ), 'fbaeo_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( fbaeo_fields() as $key => $kind ) {
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
			switch ( $kind ) {
				case 'bool':
					$value = $raw ? '1' : '';
					break;
				case 'int':
					$value = $raw ? (string) absint( $raw ) : '';
					break;
				case 'url':
					$value = esc_url_raw( $raw );
					break;
				case 'key':
					$value = sanitize_key( $raw );
					break;
				case 'slug':
					$value = sanitize_title( $raw );
					break;
				case 'textarea':
					$value = sanitize_textarea_field( $raw );
					break;
				default:
					$value = sanitize_text_field( $raw );
			}
			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
);
