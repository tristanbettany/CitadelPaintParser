@ECHO OFF

2>NUL CALL :CASE_%1
IF ERRORLEVEL 1 CALL :DEFAULT_CASE

ECHO Done!
EXIT /B

:CASE_build
    ECHO Removing temporary application files...
    @RD /S /Q "vendor"
    ECHO Building...
    docker build -t tristanbettany:cpm .
    ECHO Install dependancies...
    docker run -it --rm -v %cd%:/app -w /app tristanbettany:cpm composer install
    GOTO END_CASE

:CASE_install
    ECHO Removing temporary application files...
    @RD /S /Q "vendor"
    ECHO Install dependancies...
    docker run -it --rm -v %cd%:/app -w /app tristanbettany:cpm composer install
    GOTO END_CASE

:CASE_update
    ECHO Updating dependancies...
    docker run -it --rm -v %cd%:/app -w /app tristanbettany:cpm composer update
    GOTO END_CASE

:CASE_exec
    docker run -it --rm -v %cd%:/app -w /app tristanbettany:cpm php src/cmd.php
    GOTO END_CASE

:CASE_destroy
    ECHO Destroying...
    @RD /S /Q "vendor"
    docker stop tristanbettany:cpm
    docker rm tristanbettany:cpm
    GOTO END_CASE

:DEFAULT_CASE
    ECHO Unknown function "%1"
    GOTO END_CASE
:END_CASE
    VER > NUL
    GOTO :EOF