<div class="register">
	<h1>New Warehouse</h1>
	<table>
		<tr>
			<td>Warehouse name:</td>
			<td><input type="text" id="groupname" /></td>
			<td id="groupwrong" class="errortext hidetext">Groupname already available</td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" id="password" /></td>
			<td id="passwordwrong" class="errortext hidetext">Passwords aren't the same!</td>
			<td id="passwordmissing" class="errortext hidetext">Password missing!</td>
		</tr>
		<tr>
			<td>Repeat password:</td>
			<td><input type="password" id="password-repeat" /></td>
		</tr>
		<tr>
			<td>Description (optional):</td>
			<td><textarea rows="5" id="groupdescription"></textarea>
		</tr>
		<tr>
			<td colspan="2" align="center"><a href="javascript: addGroup();" class="button button_block">Create Warehouse</a></td>
		</tr>
	</table>
</div>