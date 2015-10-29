<?php
$output = array();
if($this->validationErrors) {
    $output = Set::insert($output, 'errors', array('message' => $errors['message']));
    $errorMessages = array(
        'TeamsUser' => array(
            'team_id' => array(
                'required' => __("TID This field cannot be left blank.", true),
                'numeric' => __("Mustr be #", true),
            ),
            'user_id' => array(
                'required' => __("UID This field cannot be left blank.", true),
                'numeric' => __("Must be # UID.", true),
            )
        )
    );
    foreach ($errors['data'] as $model => $errs) {
        foreach ($errs as $field => $message) {
            $output['errors']['data'][$model][$field] = $errorMessages[$model][$field][$message];
        }
    }
} elseif ($success) {
    $output = Set::insert($output, 'success', array(
        'message' => $success['message'],
        'data' => $success['data']
    ));
}
echo $javascript->object($output);
?>