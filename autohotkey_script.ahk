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