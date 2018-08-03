<div class="container-fluid">
	<div class="row">
		<!-- Main Heading -->
		<div class="col-md-12 col-lg-12 paddingLt0">
			<h3 class="medium-title marginTop2 marginBtm0">Menu Access Control</h3>
		</div>
		<!--./ col-md-12 -->
	</div>
	<!-- ./ row -->
</div>
<!-- ./ container -->
<!--=======
               SPACER
            ========-->
<div class="marginTop10"></div>
<div class="container-fluid">
	<div class="row">
		<div class='col-md-12 col-lg-12'>

			<table class="table table-striped table-bordered" cellspacing="0"
				width="100%">
				<thead>
					<tr>
						<th width='300'>Module</th>
						<?php
    foreach ($roles as $role) {
        echo "<th><input type='checkbox' name='RoleName' class='SelectAll' data-role='$role->RoleID' /> $role->RoleCode</th>";
    }
    ?>
 					</tr>
				</thead>
				<tbody>
				<?php
    foreach ($access_modules as $access_module) {
        echo "<tr><td>$access_module[Name]</td>";
        foreach ($roles as $role) {
            $a_role = $access_module['Role'];
            echo "<td><input type='checkbox' name='AccessRole' class='access-role' data-role='$role->RoleID' data-module='$access_module[ID]' " . ($a_role[$role->RoleID]['Access'] == 'Yes' ? 'checked' : '') . " /></td>";
        }
        echo "</tr>";
    }
    ?>
				</tbody>
			</table>
		</div>
	</div>
	<!-- ./ row -->
</div>
<?php
// Template::loadScripts($this->publicURL());
?>
