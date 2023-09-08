<?php

use Yoast\WPTestUtils\WPIntegration\TestCase;

class TestThemeFunctions extends TestCase {

	use AnsPress\Tests\Testcases\Common;

	public function pageTitle() {
		return 'Question title';
	}

	public function askBtnLink() {
		return home_url( '/ask' );
	}

	/**
	 * @covers ::ap_page_title
	 */
	public function testAPPageTitle() {
		$this->assertEquals( '', ap_page_title() );

		// Filter apply test,
		add_filter( 'ap_page_title', array( $this, 'pageTitle' ) );
		$this->assertNotEquals( '', ap_page_title() );
		$this->assertEquals( 'Question title', ap_page_title() );

		// Filter remove test,
		remove_filter( 'ap_page_title', array( $this, 'pageTitle' ) );
		$this->assertNotEquals( 'Question title', ap_page_title() );
		$this->assertEquals( '', ap_page_title() );
	}

	/**
	 * @covers ::ap_post_status
	 */
	public function testAPPostStatus() {
		$id = $this->insert_question();
		$this->assertEquals( 'publish', ap_post_status( $id ) );

		// Check for private_post post status.
		$id = $this->factory->post->create(
			array(
				'post_title'   => 'Question title',
				'post_content' => 'Question content',
				'post_status'  => 'private_post',
				'post_type'    => 'question',
			)
		);
		$post = get_post( $id );
		$this->assertEquals( 'private_post', ap_post_status( $id ) );

		// Check for moderate post status.
		$id = $this->factory->post->create(
			array(
				'post_title'   => 'Question title',
				'post_content' => 'Question content',
				'post_status'  => 'moderate',
				'post_type'    => 'question',
			)
		);
		$post = get_post( $id );
		$this->assertEquals( 'moderate', ap_post_status( $id ) );
	}

	/**
	 * @covers ::is_private_post
	 */
	public function testPrivatePost() {
		$id = $this->insert_question();
		$this->assertFalse( is_private_post( $id ) );

		// Check for private_post post status.
		$id = $this->factory->post->create(
			array(
				'post_title'   => 'Question title',
				'post_content' => 'Question content',
				'post_status'  => 'private_post',
				'post_type'    => 'question',
			)
		);
		$post = get_post( $id );
		$this->assertTrue( is_private_post( $id ) );
	}

	/**
	 * @covers ::is_post_waiting_moderation
	 */
	public function testModeratePost() {
		$id = $this->insert_question();
		$this->assertFalse( is_post_waiting_moderation( $id ) );

		// Check for moderate post status.
		$id = $this->factory->post->create(
			array(
				'post_title'   => 'Question title',
				'post_content' => 'Question content',
				'post_status'  => 'moderate',
				'post_type'    => 'question',
			)
		);
		$post = get_post( $id );
		$this->assertTrue( is_post_waiting_moderation( $id ) );
	}

	/**
	 * @covers ::is_post_closed
	 */
	public function testIsPostClosed() {
		$id = $this->insert_question();
		$this->assertFalse( is_post_closed( $id ) );

		// Check for question open.
		ap_insert_qameta(
			$id,
			array(
				'selected_id'  => '',
				'last_updated' => current_time( 'mysql' ),
				'closed'       => 0,
			)
		);
		$this->assertFalse( is_post_closed( $id ) );

		// Check for question close.
		ap_insert_qameta(
			$id,
			array(
				'selected_id'  => '',
				'last_updated' => current_time( 'mysql' ),
				'closed'       => 1,
			)
		);
		$this->assertTrue( is_post_closed( $id ) );
	}

	/**
	 * @covers ::ap_have_parent_post
	 */
	public function testAPHaveParentPost() {
		$id = $this->insert_question();
		$this->assertFalse( ap_have_parent_post( $id ) );
		$child_id = $this->factory->post->create(
			array(
				'post_title'   => 'Question title',
				'post_content' => 'Question content',
				'post_type'    => 'question',
				'post_parent'  => $id,
			)
		);
		$this->assertTrue( ap_have_parent_post( $child_id ) );
		$child_post_id = $this->factory->post->create(
			array(
				'post_parent'  => $id,
			)
		);
		$this->assertFalse( ap_have_parent_post( $child_post_id ) );
		$id = $this->insert_answer();
		$this->assertFalse( ap_have_parent_post( $id->a ) );
	}

	/**
	 * @covers ::ap_get_ask_btn
	 * @covers ::ap_ask_btn
	 */
	public function testAPAskBtn() {
		$link = ap_get_link_to( 'ask' );
		$this->assertSame( '<a class="ap-btn-ask" href="' . $link . '">Ask question</a>', ap_get_ask_btn() );
		ob_start();
		ap_ask_btn();
		$output = ob_get_clean();
		$this->assertSame( '<a class="ap-btn-ask" href="' . $link . '">Ask question</a>', $output );

		// Test for filter addition.
		add_filter( 'ap_ask_btn_link', array( $this, 'askBtnLink' ) );
		$this->assertSame( '<a class="ap-btn-ask" href="' . home_url( '/ask' ) . '">Ask question</a>', ap_get_ask_btn() );
		ob_start();
		ap_ask_btn();
		$output = ob_get_clean();
		$this->assertSame( '<a class="ap-btn-ask" href="' . home_url( '/ask' ) . '">Ask question</a>', $output );

		// Test after filter remove.
		remove_filter( 'ap_ask_btn_link', array( $this, 'askBtnLink' ) );
		$this->assertSame( '<a class="ap-btn-ask" href="' . $link . '">Ask question</a>', ap_get_ask_btn() );
		ob_start();
		ap_ask_btn();
		$output = ob_get_clean();
		$this->assertSame( '<a class="ap-btn-ask" href="' . $link . '">Ask question</a>', $output );
	}

}
