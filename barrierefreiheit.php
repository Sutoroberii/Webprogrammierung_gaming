<?php
$title = "Barrierefreiheitserklärung";
?>

<?php include_once "php/include/head.php"; ?>
<body>
    <header class="nav">
    <div class="nav-left">
    <a href="index.php" class="logo">
        
        <div class="logo-icon">
            <iconify-icon icon="game-icons:beer-stein"></iconify-icon>
        </div>

        <span class="logo-text">NPC Tavern</span>

    </a>
</div>
    <?php include_once "php/include/nav.php"; ?>
</header>

<main class="legal-main">
    <section class="legal-content">
        <h1>Erklärung zur Barrierefreiheit</h1>

        <p>
            Max Muster (Betreiber von "NPC Tavern") ist bemüht, seine Webanwendung im Einklang mit den nationalen Rechtsvorschriften zur Umsetzung der Richtlinie (EU) 2016/2102 des Europäischen Parlaments und des Rates barrierefrei zugänglich zu machen.
        </p>

        <p>Diese Erklärung zur Barrierefreiheit gilt für die unter der Domain <strong>localhost/Webprogrammierung_gaming/</strong> veröffentlichte Webanwendung "NPC Tavern".</p>

        <h2>Stand der Vereinbarkeit mit den Anforderungen</h2>

        <p>
            Diese Webanwendung ist wegen der nachstehend aufgeführten Unvereinbarkeiten und Ausnahmen <strong>teilweise</strong> mit den Anforderungen der BITV 2.0 bzw. der WCAG 2.1 (Stufe AA) vereinbar.
        </p>

        <h2>Nicht barrierefreie Inhalte</h2>

        <p>Die nachstehend aufgeführten Inhalte sind aus den folgenden Gründen nicht vollständig barrierefrei:</p>

        <ul>
            <li>
                <strong>Farbkontraste:</strong> An einigen Stellen, insbesondere bei Links, ist der Farbkontrast zum Hintergrund nicht ausreichend. Dies kann die Lesbarkeit für Menschen mit Sehbeeinträchtigungen erschweren (betrifft WCAG-Erfolgskriterium 1.4.3 Kontrast (Minimum)).
            </li>
            <li>
                <strong>Fehlende Beschriftungen für Formularelemente:</strong> Die zentrale Suchleiste im Header der Seite besitzt keine programmatisch verknüpfte Beschriftung (Label). Dies erschwert die Bedienung für Nutzer von Screenreadern (betrifft WCAG-Erfolgskriterium 3.3.2 Beschriftungen oder Anweisungen).
            </li>
            <li>
                <strong>Alternative Texte für Bilder:</strong> Von Nutzern hochgeladene Bilder haben teilweise nur generische oder keine beschreibenden Alternativtexte. Dies führt dazu, dass Nutzer von Screenreadern den Bildinhalt nicht erfassen können (betrifft WCAG-Erfolgskriterium 1.1.1 Nicht-Text-Inhalte).
            </li>
            <li>
                <strong>Fokus-Reihenfolge und Tastaturbedienbarkeit:</strong> Einige interaktive Elemente, wie das Benachrichtigungs-Icon, sind nicht per Tastatur erreichbar oder haben keinen sichtbaren Fokus-Indikator, was die Navigation ohne Maus erschwert (betrifft WCAG-Erfolgskriterium 2.4.3 Fokus-Reihenfolge und 2.4.7 Fokus sichtbar).
            </li>
        </ul>

        <p>Wir arbeiten daran, diese Barrieren zu beheben.</p>

        <h2>Erstellung dieser Erklärung zur Barrierefreiheit</h2>

        <p>Diese Erklärung wurde am 19. Juli 2026 erstellt.</p>
        <p>Die Bewertung erfolgte auf Basis einer Selbstbewertung.</p>

        <h2>Feedback und Kontaktangaben</h2>

        <p>
            Sind Ihnen weitere Mängel beim barrierefreien Zugang zu Inhalten auf unserer Seite aufgefallen? Dann können Sie sich gerne bei uns melden. Bitte nutzen Sie dafür die folgende E-Mail-Adresse:
        </p>
        <p>E-Mail: <a href="mailto:max@muster.de">max@muster.de</a></p>

        <h2>Schlichtungsverfahren</h2>
        <p>
            Sollten Sie auf unsere Mitteilungen keine zufriedenstellende Antwort erhalten haben, können Sie die Schlichtungsstelle nach § 16 BGG einschalten. Weitere Informationen zu diesem Verfahren und die Möglichkeit, einen Antrag zu stellen, finden Sie auf der Webseite der <a href="https://www.schlichtungsstelle-bgg.de" target="_blank" rel="noopener noreferrer">Schlichtungsstelle BGG</a>.
        </p>
    </section>
</main>

<footer class="footer">
    <?php include_once "php/include/footer.php"; ?>
</footer>
</body>