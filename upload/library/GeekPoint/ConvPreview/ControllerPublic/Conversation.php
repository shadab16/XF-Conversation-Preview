<?php

/**
 * Controller for handling Personal Conversations
 *
 * @see XenForo_ControllerPublic_Conversation
 * @author Shadab Ansari
 * @package GeekPoint_ConvPreview
 */
class GeekPoint_ConvPreview_ControllerPublic_Conversation extends XFCP_GeekPoint_ConvPreview_ControllerPublic_Conversation
{
	/**
	 * Displays a preview of the conversation.
	 * Returns a list of conversation participants for now.
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionPreviewParticipants()
	{
		$conversationId = $this->_input->filterSingle('conversation_id', XenForo_Input::UINT);
		$conversation = $this->_getConversationOrError($conversationId);

		$recipients = $this->_getConversationModel()->getConversationRecipients($conversationId);

		$viewParams = array(
			'conversation' => $conversation,
			'recipients' => $recipients
		);

		return $this->responseView(
			'GeekPoint_ConvPreview_ViewPublic_Conversation_PreviewParticipants',
			'conversation_list_item_preview_participants',
			$viewParams
		);
	}
}