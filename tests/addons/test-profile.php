<?php

namespace Anspress\Tests;

use Yoast\WPTestUtils\WPIntegration\TestCase;

class TestAddonProfile extends TestCase {

	use Testcases\Common;

	public function set_up() {
		parent::set_up();
		ap_activate_addon( 'profile.php' );
	}

	public function tear_down() {
		parent::tear_down();
		ap_deactivate_addon( 'profile.php' );
	}

	/**
	 * @covers Anspress\Addons\Profile::instance
	 */
	public function testInstance() {
		$class = new \ReflectionClass( 'Anspress\Addons\Profile' );
		$this->assertTrue( $class->hasProperty( 'instance' ) && $class->getProperty( 'instance' )->isStatic() );
	}

	public function testMethodExists() {
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', '__construct' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'add_to_settings_page' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'options' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'user_page' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'rewrite_rules' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'user_pages' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'user_menu' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'user_page_title' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'page_title' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'filter_page_title' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'sub_page_template' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'question_page' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'answer_page' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'load_more_answers' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'ap_current_page' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'modify_query_archive' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'page_template' ) );
		$this->assertTrue( method_exists( 'Anspress\Addons\Profile', 'current_user_id' ) );
	}

	/**
	 * @covers Anspress\Addons\Profile::instance
	 */
	public function testInit() {
		$instance1 = \Anspress\Addons\Profile::init();
		$this->assertInstanceOf( 'Anspress\Addons\Profile', $instance1 );
		$instance2 = \Anspress\Addons\Profile::init();
		$this->assertSame( $instance1, $instance2 );
	}

	/**
	 * @covers Anspress\Addons\Profile::add_to_settings_page
	 */
	public function testAddToSettingsPage() {
		$instance = \Anspress\Addons\Profile::init();

		// Call the method.
		$groups = $instance->add_to_settings_page( [] );

		// Test if the Profile group is added to the settings page.
		$this->assertArrayHasKey( 'profile', $groups );
		$this->assertEquals( 'Profile', $groups['profile']['label'] );

		// Test by adding new group.
		$groups = $instance->add_to_settings_page( [ 'some_other_group' => [ 'label' => 'Some Other Group' ] ] );

		// Test if the new group is added to the settings page.
		$this->assertArrayHasKey( 'some_other_group', $groups );
		$this->assertEquals( 'Some Other Group', $groups['some_other_group']['label'] );

		// Test if the existing group are retained to the settings page.
		$this->assertArrayHasKey( 'profile', $groups );
		$this->assertEquals( 'Profile', $groups['profile']['label'] );
	}

	/**
	 * @covers Anspress\Addons\Profile::options
	 */
	public function testOptions() {
		$instance = \Anspress\Addons\Profile::init();

		// Add user_page_slug_questions, user_page_slug_answers, user_page_title_questions and user_page_title_answers options.
		ap_add_default_options(
			array(
				'user_page_slug_questions'  => 'questions',
				'user_page_slug_answers'    => 'answers',
				'user_page_title_questions' => 'Questions',
				'user_page_title_answers'   => 'Answers',
			)
		);

		// Call the method.
		$form = $instance->options();

		// Test begins.
		$this->assertNotEmpty( $form );
		$this->assertArrayHasKey( 'user_page_title_questions', $form['fields'] );
		$this->assertArrayHasKey( 'user_page_slug_questions', $form['fields'] );
		$this->assertArrayHasKey( 'user_page_title_answers', $form['fields'] );
		$this->assertArrayHasKey( 'user_page_slug_answers', $form['fields'] );

		// Test for user_page_slug_questions.
		$this->assertArrayHasKey( 'label', $form['fields']['user_page_title_questions'] );
		$this->assertEquals( 'Questions page title', $form['fields']['user_page_title_questions']['label'] );
		$this->assertArrayHasKey( 'desc', $form['fields']['user_page_title_questions'] );
		$this->assertEquals( 'Custom title for user profile questions page', $form['fields']['user_page_title_questions']['desc'] );
		$this->assertArrayHasKey( 'value', $form['fields']['user_page_title_questions'] );
		$this->assertEquals( ap_opt( 'user_page_title_questions' ), $form['fields']['user_page_title_questions']['value'] );

		// Test for user_page_slug_answers.
		$this->assertArrayHasKey( 'label', $form['fields']['user_page_slug_questions'] );
		$this->assertEquals( 'Questions page slug', $form['fields']['user_page_slug_questions']['label'] );
		$this->assertArrayHasKey( 'desc', $form['fields']['user_page_slug_questions'] );
		$this->assertEquals( 'Custom slug for user profile questions page', $form['fields']['user_page_slug_questions']['desc'] );
		$this->assertArrayHasKey( 'value', $form['fields']['user_page_slug_questions'] );
		$this->assertEquals( ap_opt( 'user_page_slug_questions' ), $form['fields']['user_page_slug_questions']['value'] );

		// Test for user_page_title_answers.
		$this->assertArrayHasKey( 'label', $form['fields']['user_page_title_answers'] );
		$this->assertEquals( 'Answers page title', $form['fields']['user_page_title_answers']['label'] );
		$this->assertArrayHasKey( 'desc', $form['fields']['user_page_title_answers'] );
		$this->assertEquals( 'Custom title for user profile answers page', $form['fields']['user_page_title_answers']['desc'] );
		$this->assertArrayHasKey( 'value', $form['fields']['user_page_title_answers'] );
		$this->assertEquals( ap_opt( 'user_page_title_answers' ), $form['fields']['user_page_title_answers']['value'] );

		// Test for user_page_slug_answers.
		$this->assertArrayHasKey( 'label', $form['fields']['user_page_slug_answers'] );
		$this->assertEquals( 'Answers page slug', $form['fields']['user_page_slug_answers']['label'] );
		$this->assertArrayHasKey( 'desc', $form['fields']['user_page_slug_answers'] );
		$this->assertEquals( 'Custom slug for user profile answers page', $form['fields']['user_page_slug_answers']['desc'] );
		$this->assertArrayHasKey( 'value', $form['fields']['user_page_slug_answers'] );
		$this->assertEquals( ap_opt( 'user_page_slug_answers' ), $form['fields']['user_page_slug_answers']['value'] );
	}

	public function testUserPageRegistered() {
		$instance = \Anspress\Addons\Profile::init();

		// Test if user page is registered or not.
		$user_page = anspress()->pages['user'];
		$this->assertIsArray( $user_page );
		$this->assertEquals( 'User profile', $user_page['title'] );
		$this->assertEquals( [ $instance, 'user_page' ], $user_page['func'] );
		$this->assertEquals( true, $user_page['show_in_menu'] );
		$this->assertEquals( true, $user_page['private'] );
	}

	/**
	 * @covers Anspress\Addons\Profile::current_user_id
	 */
	public function testCurrentUserID() {
		$instance = \Anspress\Addons\Profile::init();

		// Test for user id without visiting the user profile page.
		$this->setRole( 'subscriber' );
		$this->assertEquals( get_current_user_id(), $instance->current_user_id() );
		$this->logout();

		// Test for user id with visiting the user profile page.
		// Test 1.
		$user = $this->factory()->user->create_and_get();
		$this->assertNotEquals( $user->ID, $instance->current_user_id() );
		$user_page = $this->factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'User profile',
			)
		);
		ap_opt( 'user_page', $user_page );
		$this->go_to( '/?post_type=page&p=' . $user_page );
		set_query_var( 'user_page', 'profile' );
		global $wp_query;
		$wp_query->queried_object = $user;
		$wp_query->queried_object_id = $user->ID;
		$this->assertEquals( $user->ID, $instance->current_user_id() );
		$this->go_to( '/' );

		// Test 2.
		wp_set_current_user( $user->ID );
		$this->assertEquals( $user->ID, $instance->current_user_id() );
		$new_user = $this->factory()->user->create_and_get();
		$this->assertNotEquals( $new_user->ID, $instance->current_user_id() );
		$this->go_to( '/?post_type=page&p=' . $user_page );
		set_query_var( 'user_page', 'profile' );
		global $wp_query;
		$wp_query->queried_object = $new_user;
		$wp_query->queried_object_id = $new_user->ID;
		$this->assertEquals( $new_user->ID, $instance->current_user_id() );
		$this->go_to( '/' );
		$this->logout();
	}

	/**
	 * @covers Anspress\Addons\Profile::ap_current_page
	 */
	public function testAPCurrentPage() {
		$instance = \Anspress\Addons\Profile::init();

		// Test by visting other pages.
		$this->go_to( '/' );
		$method = $instance->ap_current_page( 'other_query_var' );
		$this->assertEquals( 'other_query_var', $method );

		// Test by visting user profile page.
		$user_page = $this->factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'User profile',
			)
		);
		ap_opt( 'user_page', $user_page );
		$user = $this->factory()->user->create_and_get();
		$this->go_to( '/?post_type=page&p=' . $user_page );
		set_query_var( 'user_page', 'profile' );
		global $wp_query;
		$wp_query->queried_object = $user;
		$wp_query->queried_object_id = $user->ID;
		$method = $instance->ap_current_page( 'other_query_var' );
		$this->assertEquals( 'other_query_var', $method );

		// Test by visting/setting the user author archive page.
		$this->setRole( 'editor' );
		$this->go_to( get_author_posts_url( get_current_user_id() ) );
		set_query_var( 'ap_page', 'user' );
		$method = $instance->ap_current_page( 'other_query_var' );
		$this->assertEquals( 'user', $method );
	}
}