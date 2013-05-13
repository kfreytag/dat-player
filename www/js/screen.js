Screen = {};

Screen.init = function ()
{
    Screen.height = screen.height;
    Screen.width = screen.width;
};

Screen.getWidth = function()
{
	return screen.width;
};

Screen.getHeight = function()
{
	return screen.height;
};

$().ready(Screen.init());