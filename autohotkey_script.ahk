;----- IF pressed CTRL+WIN+ALT+7
;----- mapped with "7" macro key on my mouse

^!#7::
;press CTRL+C
Send , ^c
clipfile = i:\michel\shit2pdo\clip.txt
outfile = i:\michel\shit2pdo\out.txt

FileDelete,%clipfile%
FileAppend,%clipboard%,%clipfile%

;call the parser -> this will generate the "out.txt" file
UrlDownloadToFile, http://vm-dev/michel/shit2pdo/go.php, i:\michel\shit2pdo\tmp_download.txt

;read the out file to %r% variable:
FileRead, r, %outfile%

;set the clipboard
Clipboard := r

;paste (ctrl + v)
Send , ^v

;some sound
SoundPlay *64

return

^!#9::
CoordMode Pixel
ImageSearch, FoundX, FoundY, 0, 0, A_ScreenWidth, A_ScreenHeight, d:\dev\autohotkey_scripts\images\calc_5.bmp

if ErrorLevel = 2
    MsgBox Could not conduct the search.
else if ErrorLevel = 1
    MsgBox Icon could not be found on the screen.
else
    MsgBox The icon was found at %FoundX%x%FoundY%.

; ImageSearch, OutputVarX, OutputVarY, X1, Y1, X2, Y2, C:\Users\milawski\Pictures\Screenpresso\2018-06-28_12h43_45.png
return

