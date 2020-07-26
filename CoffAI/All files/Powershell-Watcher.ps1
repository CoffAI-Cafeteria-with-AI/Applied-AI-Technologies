$(
$watcherPath = ".\uploads\"
) | Out-Null
"`n--------------------------------------------------------------`n"
"Please close this Command Prompt only for Shutdown !"
"`n--------------------------------------------------------------`n"
"Folder to check for new uploaded files: $watcherPath"
"`n"
"When a file is created in this directory, the Cafeteria-AI website opens and the artificial intelligence runs."
$(
# https://qastack.com.de/superuser/226828/how-to-monitor-a-folder-and-trigger-a-command-line-action-when-a-file-is-created-or-edited

    if (!(Test-Path $watcherPath)) {New-Item -Path $watcherPath -ItemType Directory}

### SET FOLDER TO WATCH + FILES TO WATCH + SUBFOLDERS YES/NO
    $watcher = New-Object System.IO.FileSystemWatcher
    $watcher.Path = $watcherPath
    $watcher.Filter = "*.*"
    $watcher.IncludeSubdirectories = $true
    $watcher.EnableRaisingEvents = $true  

### DEFINE ACTIONS AFTER AN EVENT IS DETECTED
    $action = { $path = $Event.SourceEventArgs.FullPath
                $changeType = $Event.SourceEventArgs.ChangeType
                $fileName = $Event.SourceEventArgs.Name
                $url = "http://localhost/cafeteriaAI_normalMode.php"
                $url = $url + "?fileToUpload=" + $fileName
                Start-Process $url
                $logline = "$(Get-Date), $changeType, $path, $url"
                Add-content ".\Powershell-Watcher_LOG.txt" -value $logline
              }

### DECIDE WHICH EVENTS SHOULD BE WATCHED
    Register-ObjectEvent $watcher "Created" -Action $action
    # Register-ObjectEvent $watcher "Changed" -Action $action
    # Register-ObjectEvent $watcher "Deleted" -Action $action
    # Register-ObjectEvent $watcher "Renamed" -Action $action
    while ($true) { Start-Sleep 1 }
) | Out-Null