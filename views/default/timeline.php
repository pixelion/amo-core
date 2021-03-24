<?php
/**
 * @var $this \yii\web\View
 */
foreach ($items as $key => $item) {

    $isEven = ($items[$key - 1]->element_id == $item->element_id) ? '' : 'timeline-left';
    if (isset($items[$key + 1])) {
        $isEven = ($items[$key + 1]->element_id == $item->element_id) ? '' : 'timeline-left';
    }
    // $isEven = ($items[$key - 1]->responsible_user_id == $item->responsible_user_id) ? '' : 'timeline-left2';

    echo $this->render('@app/views/site/'.$item->getRowStyle(), ['item' => $item, 'isEven' => $isEven]);

    ?>

<?php }
?>