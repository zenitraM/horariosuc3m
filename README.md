HorariosUC3M
================

This is the code powering http://horariosuc3m.itram.es, a parser that converts timetables from the [UC3M Horariosweb application](https://aplicaciones.uc3m.es/horarios-web/publicacion/principal.page) to .ics files to be imported into any calendar app.

This project is horrid PHP I wrote back in 2010, and that I've since only changed every year to regenerate the static subjects data and change the constants that specify the current academic year in some places across the code. Luckily, the UC3M Horarios site hasn't changed since either.

Running
---------
The `data/` folder contains static information parsed from several places of the university site, such as the mapping between subject codes and their "pretty names", and the tree of courses. Run `php info.php` to regenerate it.

Apart from that, you only need to put it on a PHP5 web server, and make sure PHP has permissions to write on the `out/` folder to generate the .ics files, which are served statically once generated. 

You also need to make sure .ics files are served with UTF-8 encoding and `text/calendar` Content-type, to avoid tildes and eñes appearing weirdly. I use this on Nginx:

```
	charset utf-8;
	charset_types text/calendar;
```

End of requirements. I run this on a [3$/year, 128mb RAM VPS](http://lowendspirit.com/locations.html), so with minimal effort this will run even on a potato. I wish the same thing could be said about most web things we create today.
