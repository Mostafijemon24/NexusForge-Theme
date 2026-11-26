<?php
/**
 * Text used by the workspace scripts.
 *
 * @package FusionWrite_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'networkError'      => __( 'The request did not reach the site. Check the connection and try again.', 'fusionwrite-ai' ),
	'serverError'       => __( 'The server stopped before it could answer, most often a time limit on the hosting. Try a shorter post or a faster provider.', 'fusionwrite-ai' ),
	'badAnswer'         => __( 'The site sent an answer that could not be read. Reload the page and try again.', 'fusionwrite-ai' ),
	'needProvider'      => __( 'Connect an AI provider first, on the Providers page.', 'fusionwrite-ai' ),
	'needTopic'         => __( 'Enter a topic for the post.', 'fusionwrite-ai' ),
	'needTopics'        => __( 'Enter at least one topic.', 'fusionwrite-ai' ),
	/* translators: %s: number of words. */
	'aboutWords'        => __( 'About %s words', 'fusionwrite-ai' ),
	'stepWrite'         => __( 'Writing the post', 'fusionwrite-ai' ),
	'stepFeatured'      => __( 'Creating the featured image', 'fusionwrite-ai' ),
	/* translators: 1: image number, 2: number of images. */
	'stepImage'         => __( 'Creating image %1$s of %2$s', 'fusionwrite-ai' ),
	/* translators: %s: seconds left. */
	'waitingImages'     => __( 'The image service asks for a pause between images. Continuing in %s seconds.', 'fusionwrite-ai' ),
	/* translators: %s: seconds left. */
	'waitingProvider'   => __( 'The provider asked for a pause. Trying again in %s seconds.', 'fusionwrite-ai' ),
	/* translators: 1: number of words, 2: model name. */
	'wordsBy'           => __( '%1$s words, written by %2$s.', 'fusionwrite-ai' ),
	/* translators: %s: the topic. */
	'writingTopic'      => __( 'Writing “%s”', 'fusionwrite-ai' ),
	'saved_draft'       => __( 'Saved as a draft.', 'fusionwrite-ai' ),
	'saved_pending'     => __( 'Saved for review.', 'fusionwrite-ai' ),
	'saved_publish'     => __( 'Published.', 'fusionwrite-ai' ),
	/* translators: %s: number of images. */
	'imagesMissing'     => __( 'Images that could not be made: %s.', 'fusionwrite-ai' ),
	'reviewTitle'       => __( 'Review the post', 'fusionwrite-ai' ),
	'reviewLede'        => __( 'It is already saved in WordPress. Change anything you like here, then save again.', 'fusionwrite-ai' ),
	'oneTopic'          => __( '1 topic', 'fusionwrite-ai' ),
	/* translators: %s: number of topics. */
	'manyTopics'        => __( '%s topics', 'fusionwrite-ai' ),
	'writeOnePost'      => __( 'Write posts', 'fusionwrite-ai' ),
	/* translators: %s: number of posts. */
	'writePosts'        => __( 'Write %s posts', 'fusionwrite-ai' ),
	'queued'            => __( 'Waiting', 'fusionwrite-ai' ),
	'notWritten'        => __( 'Not written', 'fusionwrite-ai' ),
	/* translators: 1: position in the queue, 2: number of posts. */
	'queueProgress'     => __( 'Writing post %1$s of %2$s', 'fusionwrite-ai' ),
	/* translators: 1: posts written, 2: posts requested. */
	'queueDone'         => __( '%1$s of %2$s posts written', 'fusionwrite-ai' ),
	'stopping'          => __( 'Stopping after this post', 'fusionwrite-ai' ),
	'edit'              => __( 'Edit', 'fusionwrite-ai' ),
	/* translators: %s: number of models. */
	'modelsLoaded'      => __( '%s models loaded. Click the Model field and start typing to search them.', 'fusionwrite-ai' ),
	/* translators: 1: models listed, 2: free models. */
	'modelsLoadedFree'  => __( '%1$s models listed, %2$s of them free. Click the Model field and start typing to search them.', 'fusionwrite-ai' ),
	'testing'           => __( 'Saving, then sending a short test request.', 'fusionwrite-ai' ),
	/* translators: %s: error message. */
	'savedButFailed'    => __( 'Saved, but the test failed. %s', 'fusionwrite-ai' ),
	/* translators: %s: what still needs doing. */
	'savedBut'          => __( 'Saved. %s', 'fusionwrite-ai' ),
	'connected'         => __( 'Connected', 'fusionwrite-ai' ),
	'connectedDefault'  => __( 'Connected, used by default', 'fusionwrite-ai' ),
	'confirmDisconnect' => __( 'Remove the saved key for this provider?', 'fusionwrite-ai' ),
	'makingSample'      => __( 'Creating a sample image. This can take up to a minute.', 'fusionwrite-ai' ),
	'sampleReady'       => __( 'This is what the service returns. The sample is not saved.', 'fusionwrite-ai' ),
	/* translators: 1: error message, 2: seconds to wait. */
	'sampleWait'        => __( '%1$s Try again in %2$s seconds.', 'fusionwrite-ai' ),
	'tourNext'          => __( 'Next', 'fusionwrite-ai' ),
	'tourBack'          => __( 'Back', 'fusionwrite-ai' ),
	'tourSkip'          => __( 'Leave tour', 'fusionwrite-ai' ),
	'tourFinish'        => __( 'Finish', 'fusionwrite-ai' ),
	/* translators: 1: current step, 2: number of steps. */
	'tourCount'         => __( 'Step %1$s of %2$s', 'fusionwrite-ai' ),
);
