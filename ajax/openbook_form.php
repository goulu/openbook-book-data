<div id="openbook-form">
	<div id="openbook-request">
		<table id="openbook-table" class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Book Number' ); ?></th>
				<td>
					<select name="openbook-booknumbertype" id="openbook-booknumbertype">
						<option value="ISBN"><?php _e( 'ISBN (10 or 13 digits)' ); ?></option>
						<option value="LCCN"><?php _e( 'LCCN' ); ?></option>
						<option value="OCLC"><?php _e( 'OCLC' ); ?></option>
						<option value="OLID"><?php _e( 'Open Library Key (OLXXXXXXXXX)' ); ?></option>
					</select>
					<input type="text" name="openbook-booknumber" id="openbook-booknumber" value="" />
					<p class="description">
						<?php
						printf( 
							__( 'Select type (usually ISBN) and enter number. You can look the number up at %s. If the book is not there, %s.' ),
							'<a href="http://openlibrary.org/" target="_blank">' . __('Open Library') . '</a>',
							'<a href="http://openlibrary.org/books/add" target="_blank">' . __('add it') . '</a>'
						); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Revision Number'); ?></th>
				<td>
					<input type="text" name="openbook-revisionnumber" id="openbook-revisionnumber" value="" disabled="true" />
					<p class="description"><?php _e( 'If the Book Number type is Open Library Key, you can specify a revision number. Otherwise the most recent version is used.' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Template Number' ); ?></th>
				<td><select name="openbook-templatenumber" id="openbook-templatenumber">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					</select>
					<p class="description">
						<?php printf(
							__( 'Select an OpenBook template number. Matches the template on the %s page' ),
							'<a href="../wp-admin/options-general.php?page=openbook_options.php" target="_blank">' . __('OpenBook Settings') . '</a>'
						); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Publisher URL' ); ?></th>
				<td>
					<input type="text" name="openbook-publisherurl" id="openbook-publisherurl" value="" />
					<p class="description"><?php _e( 'Optional. If you enter a publisher URL it will be used in the OpenBook publisher display element.' ) ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('HTML (recommended) or Shortcode' ); ?></th>
				<td>
					<input type="radio" name="openbook-shortcode" id="openbook-html" value="html" checked />
					<label for="openbook-html"><?php _e( 'HTML' ) ?></label>
					<input type="radio" name="openbook-shortcode" id="openbook-shortcode" value="shortcode" />
					<label for="openbook-shortcode"><?php _e( 'Shortcode' ) ?></label>
					<p class="description"><?php _e( 'HTML is longer but loads faster for your readers. Shortcode is shorter but makes a live call to Open Library.' ); ?></p>
				</td>
			</tr>
		</table>
	</div>
	<div id="openbook-response-div" style="display:none;visibility:hidden;">
		<span id="openbook-response"></span>
	</div>
</div>
