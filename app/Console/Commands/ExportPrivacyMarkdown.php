<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GlobalController;

class ExportPrivacyMarkdown extends Command
{
    protected $signature = 'export:privacy-markdown';
    protected $description = 'Exportiert die privacy-Tabelle als Markdown-Datei mit Inhaltsverzeichnis und Anchors';

    public function handle()
    {
        GlobalController::SetDomain();
        $entries = DB::table('oliver_rein.privacy')->where("xico_doms","ab_mcsl")->orderBy('ordering',"DESC")->get();

        if ($entries->isEmpty()) {
            $this->error('Keine Einträge in der Tabelle "privacy" gefunden.');
            return;
        }

        $markdown = "# Datenschutzerklärung\n\n## Inhaltsverzeichnis\n\n";

        // Inhaltsverzeichnis aufbauen
        $o = 1;
        foreach ($entries as $entry) {
            $anchor = $entry->slug ?? Str::slug($entry->headline);
            $markdown .= "- [$o)&nbsp; $entry->headline](#{$anchor})\n";
            $o++;
        }

        $markdown .= "\n---\n\n";

        // Abschnitte
        $i = 1;
        foreach ($entries as $entry) {
            $anchor = $entry->slug ?? Str::slug($entry->headline);

            $markdown .= "<a id=\"{$anchor}\"></a>\n";
            $markdown .= "## <span class='dark:text-layout-night-1050 text-layout-sun-1000'>$i)&nbsp;{$entry->headline}</span>\n\n";
            $markdown .= $this->convertToMarkdown($entry->message) . "\n\n";
            $markdown .= "---\n\n";
            $i++;
        }

        // Datei speichern
        Storage::disk('md')->put('privacy_ab.md', $markdown);
        $this->info("Markdown-Datei wurde unter storage/app/privacy_ab.md gespeichert.");
    }

    protected function convertToMarkdown(string $message): string
    {
        // Optional: HTML zu Markdown konvertieren
        // Hier sehr einfach gehalten – kann bei Bedarf z. B. mit `league/html-to-markdown` ersetzt werden

        $text = strip_tags($message, '<br><ul><ol><li><strong><em><b><i><a>');
        $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $text);

        return $text;
    }
}
