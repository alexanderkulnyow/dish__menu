<?php
/**
 * Plugin Name: dds-menu
 * Description: Добавляет возможность выводить меню на сайте
 * Plugin URI:  https://github.com/alexanderkulnyow/dds-menu
 * Author URI:  https://dds.by/
 * Author:      alexander kulnyow
 *
 * Text Domain: dds-banner
 * Domain Path: Путь до MO файла (относительно папки плагина)
 *
 * Requires PHP: 5.4
 * Requires at least: 2.5
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     true - активирует плагин для всей сети
 * Version:     1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly

/*
|--------------------------------------------------------------------------
| DEFINE THE CUSTOM POSTTYPE
|--------------------------------------------------------------------------
*/

/**
 * Setup dds-menu Custom Posttype
 *
 * @since       1.0
 */

add_action( 'init', 'register_post_types' );

function register_post_types() {
	register_post_type( 'dish-menu', array(
		'label'              => null,
		'labels'             => array(
			'name'               => 'Меню', // основное название для типа записи
			'singular_name'      => 'Блюдо', // название для одной записи этого типа
			'add_new'            => 'Добавить блюдо', // для добавления новой записи
			'add_new_item'       => 'Добавление Блюд', // заголовка у вновь создаваемой записи в админ-панели.
			'edit_item'          => 'Редактирование Блюда', // для редактирования типа записи
			'new_item'           => 'Новое блюдо', // текст новой записи
			'view_item'          => 'Смотреть блюдо', // для просмотра записи этого типа.
			'search_items'       => 'Искать ____', // для поиска по этим типам записи
			'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
			'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
			'menu_name'          => 'Меню', // название меню
		),
		'description'        => '',
		'public'             => true,
		'publicly_queryable' => true,
		// зависит от public
		// 'exclude_from_search' => null, // зависит от public
		'show_ui'            => true,
		// зависит от public
		// 'show_in_nav_menus'   => null, // зависит от public
		'show_in_menu'       => true,
		// показывать ли в меню адмнки
		// 'show_in_admin_bar'   => null, // зависит от show_in_menu
		'show_in_rest'       => null,
		// добавить в REST API. C WP 4.7
		'rest_base'          => null,
		// $post_type. C WP 4.7
		'menu_position'      => 4,
		'menu_icon'          => 'dashicons-clipboard',
		'capability_type'    => 'post',
//		'capabilities'      => 'menus', // массив дополнительных прав для этого типа записи
		'map_meta_cap'       => true,
		// Ставим true чтобы включить дефолтный обработчик специальных прав
		'hierarchical'       => false,
		'supports'           => [ 'title', 'editor', 'thumbnail' ],
		// 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
		'taxonomies'         => [ 'category', 'menu_taxonomy' ],
		'has_archive'        => true,
		'rewrite'            => true,
		'query_var'          => true,
	) );

}

function my_remove_wp_seo_meta_box() {
	remove_meta_box( 'wpseo_meta', 'dish-menu', 'normal' );
}

add_action( 'add_meta_boxes', 'my_remove_wp_seo_meta_box', 100 );

add_action( 'add_meta_boxes', 'menu_add_custom_box' );
function menu_add_custom_box() {
	$screens = array( 'dish-menu' );
	add_meta_box( 'menu_sectionid', 'Информация', 'menu_meta_box_callback', $screens );
}

function menu_meta_box_callback( $post ) {
	wp_nonce_field( 'menu_save_postdata', 'menu_noncename' );

	$key_menu_cost   = get_post_meta( $post->ID, 'menu-cost', 1 );
	$key_menu_weight = get_post_meta( $post->ID, 'menu-weight', 1 );
	?>
    <style>
        .queen__meta {
            display: block;
        }

        .queen__meta label {
            display: inline-block;
            width: 160px;
            text-align: left;
        }

        .queen__meta input {
            display: inline-block;
            width: 200px;
            text-align: left;
        }
    </style>
    <div class="queen__meta options_group">
        <p class="queen__meta-item">
            <label for="name_menu_cost">Стоимость:</label>
            <input id="name_menu_cost" type="text" name="name_menu_cost"
                   value="<?php echo $key_menu_cost; ?>"/>
        </p>
        <p class="queen__meta-item">
            <label for="name_menu_weight">Выход:</label>
            <input id="name_menu_weight" type="text" name="name_menu_weight"
                   value="<?php echo $key_menu_weight; ?>"/>
        </p>

    </div>
	<?php
}

## Сохраняем данные, когда пост сохраняется
add_action( 'save_post', 'menu_save_postdata' );
function menu_save_postdata( $post_id ) {
	// Убедимся что поле установлено.

	if ( ! isset( $_POST['menu_noncename'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['menu_noncename'], 'menu_save_postdata' ) ) {
		return;
	}
	// если это автосохранение ничего не делаем
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// проверяем права юзера
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['name_menu_cost'] ) ) {
		return;
	}
	if ( ! isset( $_POST['name_menu_weight'] ) ) {
		return;
	}

	$data_q_phone = sanitize_text_field( $_POST['name_menu_cost'] );
	$data_q_visit = sanitize_text_field( $_POST['name_menu_weight'] );

	update_post_meta( $post_id, 'menu-cost', $data_q_phone );
	update_post_meta( $post_id, 'menu-weight', $data_q_visit );
}

add_filter( 'template_include', 'menu_post_type_templates' );
function menu_post_type_templates( $template ) {
	if ( is_category(array('banketnoe-menu', 'menu'))  ) {
		$template = plugin_dir_path( __FILE__ ) . 'templates/dish-menu.php';
	}

	return $template;
}

add_action( 'init', 'menu_taxonomy' );
function menu_taxonomy() {

	// список параметров: wp-kama.ru/function/get_taxonomy_labels
	register_taxonomy( 'Категории', [ 'dish-menu' ], [
		'label'              => '',
		// определяется параметром $labels->name
		'labels'             => [
			'name'              => 'Категории',
			'singular_name'     => 'Категории',
			'search_items'      => 'Поиск категории',
			'all_items'         => 'Все категории',
			'view_item '        => 'Просмотр категории',
			'parent_item'       => 'Родитель',
			'parent_item_colon' => 'Родительская категория:',
			'edit_item'         => 'Редактировать категорию',
			'update_item'       => 'Обновить',
			'add_new_item'      => 'Добавить категорию',
			'new_item_name'     => 'Новое имя',
			'menu_name'         => 'Категории',
		],
		'description'        => '',
		// описание таксономии
		'public'             => true,
		'publicly_queryable' => null,
		// равен аргументу public
		'show_in_nav_menus'  => true,
		// равен аргументу public
		'show_ui'            => true,
		// равен аргументу public
		'show_in_menu'       => true,
		// равен аргументу show_ui
		'show_tagcloud'      => true,
		// равен аргументу show_ui
		'show_in_quick_edit' => null,
		// равен аргументу show_ui
		'hierarchical'       => true,

		'rewrite'           => true,
//		'query_var'             => $taxonomy1, // название параметра запроса
		'capabilities'      => array(),
		'meta_box_cb'       => null,
		// html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
		'show_admin_column' => false,
		// авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
		'show_in_rest'      => null,
		// добавить в REST API
		'rest_base'         => null,

	] );
}
