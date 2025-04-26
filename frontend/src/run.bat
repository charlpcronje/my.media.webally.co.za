@echo off
REM Run this from your frontend/src directory

for /r %%f in (*.js) do (
  findstr /i /c:"<" "%%f" >nul
  if not errorlevel 1 (
    ren "%%f" "%%~nf.jsx"
    echo Renamed %%f to %%~nf.jsx
  )
)