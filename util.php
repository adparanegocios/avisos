<?php

function printvardie($args) {
	$args = func_get_args ();
	$dbt = debug_backtrace ();
	$linha = $dbt [0] ['line'];
	$arquivo = $dbt [0] ['file'];
	echo "<fieldset style='border:1px solid; border-color:#F00;background-color:#FFF000;legend'><b>Arquivo:</b>" . $arquivo . "<b><br>Linha:</b><legend><b>Debug On : printvardie ( )</b></legend> $linha</fieldset>";
	
	foreach ( $args as $idx => $arg ) {
		echo "<fieldset style='background-color:#CBA; border:1px solid; border-color:#00F;'><legend><b>ARG[$idx]</b></legend>";
		echo "<pre style='background-color:#CBA; width:100%; heigth:100%;'>";
		print_r ( $arg );
		echo "</pre>";
		echo "</fieldset><br>";
	}
	die ();
}

function mostra_erros() {
	print ini_set ( "display_errors", true );
}

?>