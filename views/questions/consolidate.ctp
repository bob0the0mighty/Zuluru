<div style="padding:1em;">
<ol>
<?php
foreach ($matches as $one => $match) {
	foreach ($match as $two => $reason) {
		echo '<li>';
		if ($reason === true) {
			echo "Merge $two into $one";
		} else {
			echo $this->Html->link ($one, array('controller' => 'questions', 'action' => 'edit', 'question' => $one)) . ' vs ' .
				$this->Html->link ($two, array('controller' => 'questions', 'action' => 'edit', 'question' => $two)) . ': ' . $reason;
		}
		echo '</li>';
	}
}
?>
</ol>
</div>
