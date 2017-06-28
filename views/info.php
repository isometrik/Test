<h1>TemplateVar</h1>
<img src="<?php echo $this->logo()?>" class="tplVar_logo" alt="<?php echo $this->text('alt_logo')?>">
<p>Version: <?php echo $this->version()?></p>
<p>
    Basiccode Plugin Copyright 2017 <a href="http://3-magi.net/" target="_blank">Christoph M.
    Becker</a><br>
	Copyright 2017 isometric
</p>
<p>
    Powered by <a href="http://fontawesome.io" target="_blank">Font Awesome by
    Dave Gandy</a>.
</p>
<p class="tplVar_license">
    TemplateVar_XH is free software: you can redistribute it and/or modify it under the
    terms of the GNU General Public License as published by the Free Software
    Foundation, either version 3 of the License, or (at your option) any later
    version.
</p>
<p class="tplVar_license">
    TemplateVar_XH is distributed in the hope that it will be useful, but <em>without any
    warranty</em>; without even the implied warranty of <em>merchantability</em>
    or <em>fitness for a particular purpose</em>. See the GNU General Public
    License for more details.
</p>
<p class="tplVar_license">
    You should have received a copy of the GNU General Public License along with
    TemplateVar_XH. If not, see <a href="http://www.gnu.org/licenses/"
    target="_blank">http://www.gnu.org/licenses/</a>.
</p>
<div class="tplVar_syscheck">
    <h2><?php echo $this->text('syscheck_title')?></h2>
<?php foreach ($this->checks as $check):?>
    <p class="xh_<?php echo $this->escape($check->state)?>"><?php echo $this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>
