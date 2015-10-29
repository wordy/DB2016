<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Console.Templates.default.views
 * @since         CakePHP(tm) v 1.2.0.5234
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>

<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="<?php echo $pluralVar; ?> form">
		
			<?php echo "<?php echo \$this->Form->create('{$modelClass}', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>\n"; ?>
				<fieldset>
					<h2><?php printf("<?php echo __('%s %s'); ?>", Inflector::humanize($action), $singularHumanName); ?></h2>
			<?php
				foreach ($fields as $field) {
					if (strpos($action, 'add') !== false && $field == $primaryKey) {
						continue;
					} elseif (!in_array($field, array('created', 'modified', 'updated'))) {
						echo "<div class=\"form-group\">\n";
						echo "\t<?php echo \$this->Form->label('{$field}', '{$field}');?>\n";
						echo "\t\t<?php echo \$this->Form->input('{$field}', array('class' => 'form-control')); ?>\n";
						echo "</div><!-- .form-group -->\n";
						echo "\n";
					}
				}
				if (!empty($associations['hasAndBelongsToMany'])) {
					foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
						echo "\t\t<?php echo \$this->Form->input('{$assocName}');?>\n";
					}
				}
			?>
				</fieldset>
			<?php
				echo "<?php echo \$this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>\n";
				echo "<?php echo \$this->Form->end(); ?>\n";
			?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
