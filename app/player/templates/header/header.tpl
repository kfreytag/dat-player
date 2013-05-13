<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{if $meta_properties}
    {foreach from=$meta_properties key=key item=value}
        <meta property="{$key}" content="{$value}" />
    {/foreach}
{/if}
{if $title}
    <title>{$title|h}</title>
{/if}
{if $css}
    {foreach from=$css item=css_path}
        <link rel="stylesheet" type="text/css" href="{$css_path}"/>
    {/foreach}
{/if}
{if $js}
	{foreach from=$js item=js_path}
		<script type="text/javascript" src="{$js_path}"></script>
	{/foreach}
{/if}
{if $inlineJS}
    <script type="text/javascript">
	{foreach from=$inlineJS item=inline_script}
        {$inline_script}
    {/foreach}
	</script>
{/if}
</head>

<body>