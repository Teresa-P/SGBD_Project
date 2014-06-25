<?php 
require_once("custom/php/common.php");

function showUnitTable()
{
	global $wpdb;
	$results = $wpdb->get_results("select id, name from prop_unit_type order by name");
		
	if($wpdb->num_rows > 0)
	{
		?>
			<table class = "tables" >
				<th style="font-weight: bold;">Id</th>
				<th style="font-weight: bold;">Unidade</th>
				<?php 
				foreach ($results as $res)
				{
					$id = $res->id;
					$unity = $res->name;
					?>
					<tr>
						<td><?php echo $id; ?></td>
						<td><?php echo $unity; ?></td>
					</tr>
					<?php 
				}
				?>
			</table>
		<?php
	}
	else
	{
		?>
		<div>
			<i>Não há tipos de unidades.</i>	
		</div>
		<?php 
	} 
}

function insertSection()
{
	?>
	<div>
		<h3><i>Gestão de unidades - Introdução</i></h3>
		<div>
			<form name='register' id='register' action='gestao-de-unidades' method='post' class="validate">
				<div>
					<label for='unity'class='labelDefault'>Nome:</label>
					<input type = 'text' class='textboxDefault required word' name ='unity' placeholder = 'Unidade'/>
				</div>
				<input type = 'hidden' name = 'state' value ='inserir'/><br>
				<input type = 'submit' class="forms action"/><br>
			</form>
            
		</div>
	</div>
	<?php 
}

function stateInsert()
{
	global $wpdb;
	$unity = $_POST['unity'];
	if(empty($unity))
	{
		echo "Dados Incorretos, volte a preencher.";
		?>
		<br>
		<script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
  		</script>
  		<?php
	}
	else
	{
		?>
		<div>
			<h3><i>Gestão de unidades - Inserção</i></h3>
		</div>
		<?php 
		$result = $wpdb->query("insert into prop_unit_type(id, name) VALUES (NULL, '$unity')");
		
		if($result)
		{
			?>
				<div>
					<i>Inseriu os dados do novo tipo de unidade com sucesso.</i><br>
					<i>Clique em <a href="gestao-de-unidades"><b>Continuar</b></a> para avançar.</i>
				</div>
			<?php 
		}
		else 
		{
			insertError();
		}
	}
	
}

if(is_user_logged_in())
{
	if(current_user_can("manage_unit_types"))
	{
		switch ($_POST['state'])
		{
			case "inserir":
				stateInsert();
				break;
			default:
				showUnitTable();
				insertSection();
				break;
		}
	}
	else
	{
		?>
		<div>Não tem autorização para ver esta página!</div>
		<?php 
	}
}
else 
{
	?>
	<div>Tem de realizar o login para vizualizar esta página!</div>
	<?php
}
?>