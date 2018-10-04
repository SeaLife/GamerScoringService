<?php

/*****************************//*

This configuration can be used if environment variables are not safe to use.
The file can be mounted through the '-v' argument in docker.
If you'r not using docker, you can overwrite it in the root directory of the deployed app.

/*******************************/

setenv("SYSTEM_DEBUG", TRUE);

setenv("SYSTEM_PLUMBOK_CACHE_ENABLED", FALSE);

setenv("THEME", "united");

setenv("PASSWORD_SECURITY", 5);
