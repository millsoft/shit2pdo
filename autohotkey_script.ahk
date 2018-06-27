;----- IF pressed CTRL+WIN+ALT+7
;----- mapped with "7" macro key on my mouse

^!#7::
;press CTRL+C
Send , ^c
clipfile = d:\htdocs\sqlparser\clip.txt
outfile = d:\htdocs\sqlparser\out.txt

FileDelete,%clipfile%
FileAppend,%clipboard%,%clipfile%

;call the parser -> this will generate the "out.txt" file
UrlDownloadToFile, http://localhost/shit2pdo/go.php, d:\htdocs\sqlparser\tmp_download.txt

;read the out file to %r% variable:
FileRead, r, %outfile%

;set the clipboard
Clipboard := r

;paste (ctrl + v)
Send , ^v

;some sound
;SoundPlay *64

return