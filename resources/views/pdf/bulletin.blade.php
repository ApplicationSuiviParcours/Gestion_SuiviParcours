<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin - {{ $bulletin->eleve->nom }} {{ $bulletin->eleve->prenom ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .page {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 15mm;
        }

        /* En-tête */
        .header {
            text-align: center;
            border-bottom: 3px solid #1F4788;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #1F4788;
            font-size: 24pt;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .contact-info {
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }

        .header .date-location {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 9pt;
            font-style: italic;
        }

        /* Titre bulletin */
        .bulletin-title {
            text-align: center;
            margin: 25px 0;
        }

        .bulletin-title h2 {
            color: #1F4788;
            font-size: 20pt;
            margin-bottom: 8px;
        }

        .bulletin-title .periode {
            font-size: 12pt;
            font-weight: bold;
        }

        /* Informations élève */
        .info-eleve {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .info-eleve td {
            padding: 10px 12px;
            border: 1px solid #ddd;
        }

        .info-eleve td.label {
            background-color: #E8F4F8;
            font-weight: bold;
            width: 25%;
        }

        .info-eleve td.value {
            background-color: #fff;
            width: 25%;
        }

        /* Tableau des notes */
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .notes-table thead {
            background-color: #1F4788;
            color: white;
        }

        .notes-table th {
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #1F4788;
            font-size: 10pt;
        }

        .notes-table tbody tr {
            border: 1px solid #ccc;
        }

        .notes-table tbody tr:nth-child(even) {
            background-color: #F8F9FA;
        }

        .notes-table tbody tr:nth-child(odd) {
            background-color: #fff;
        }

        .notes-table td {
            padding: 8px;
            border: 1px solid #ccc;
            font-size: 10pt;
        }

        .notes-table td.matiere {
            text-align: left;
            font-weight: 500;
        }

        .notes-table td.note {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }

        .notes-table td.note.pass {
            color: #008000;
        }

        .notes-table td.note.fail {
            color: #FF0000;
        }

        .notes-table td.note.empty {
            color: #999;
            font-style: italic;
        }

        .notes-table td.coefficient {
            text-align: center;
        }

        .notes-table td.moyenne-matiere {
            text-align: center;
            font-weight: bold;
            background-color: #FFF9E6;
            font-size: 11pt;
        }

        /* Résultats */
        .resultats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .appreciation-box {
            flex: 2;
            background-color: #FFF9E6;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
        }

        .appreciation-box h3 {
            font-size: 11pt;
            margin-bottom: 10px;
            color: #1F4788;
        }

        .appreciation-box p {
            font-style: italic;
            line-height: 1.6;
        }

        .moyenne-box {
            flex: 1;
            background-color: #E8F8E8;
            border: 1px solid #ccc;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .moyenne-box h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }

        .moyenne-box .moyenne {
            font-size: 22px;
            font-weight: bold;
            color: #008000;
        }

        /* Signatures et QR Code */
        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .qr-section {
            text-align: center;
            flex: 1;
        }

        .qr-section h4 {
            font-size: 9pt;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .qr-section svg {
            border: 2px solid #1F4788;
            padding: 10px;
            border-radius: 8px;
            background: white;
        }

        .qr-section .qr-info {
            font-size: 8pt;
            margin-top: 8px;
            color: #666;
        }

        .signature-section {
            text-align: center;
            flex: 1;
        }

        .signature-section h4 {
            font-size: 10pt;
            margin-bottom: 50px;
        }

        .signature-section .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            margin: 0 auto;
        }

        /* Footer */
        .document-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 8pt;
            color: #999;
            font-style: italic;
        }

        /* Impression */
        @media print {
            body {
                padding: 0;
            }
            .page {
                max-width: 100%;
                padding: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- En-tête --}}
        <div class="header">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 60px; margin-bottom: 10px;">
            @endif
            
            <h1>{{ config('app.school_name', 'École Exemple') }}</h1>
            
            <div class="contact-info">
                Adresse : {{ config('app.school_address', '123 Rue de l\'Éducation') }} | 
                Tél: {{ config('app.school_phone', '000-000-000') }} | 
                Email: {{ config('app.school_email', 'contact@ecole.com') }}
            </div>
            
            <div class="date-location">
                <span>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                <span>Ville : {{ config('app.school_city', 'Brazzaville') }}</span>
            </div>
        </div>

        {{-- Titre du bulletin --}}
        <div class="bulletin-title">
            <h2>BULLETIN DE NOTES</h2>
            <div class="periode">Du {{ $bulletin->periode }}</div>
        </div>

        {{-- Informations de l'élève --}}
        <table class="info-eleve">
            <tr>
                <td class="label">Matricule</td>
                <td class="value">{{ $bulletin->eleve->matricule ?? 'N/A' }}</td>
                <td class="label">Classe</td>
                <td class="value">{{ $bulletin->classe->nom_classe }}</td>
            </tr>
            <tr>
                <td class="label">Nom</td>
                <td class="value"><strong>{{ $bulletin->eleve->nom }}</strong></td>
                <td class="label">Prénom</td>
                <td class="value"><strong>{{ $bulletin->eleve->prenom ?? '' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Année scolaire</td>
                <td class="value" colspan="3">{{ $bulletin->annee->libelle }}</td>
            </tr>
        </table>

        {{-- Tableau des notes avec Devoir et Examen --}}
        <table class="notes-table">
            <thead>
                <tr>
                    <th style="width: 30%">Matière</th>
                    <th style="width: 12%">Devoir<br>/20</th>
                    <th style="width: 12%">Examen<br>/20</th>
                    <th style="width: 8%">Coef.</th>
                    <th style="width: 12%">Moyenne<br>Matière</th>
                    <th style="width: 13%">Moyenne<br>Coef.</th>
                    <th style="width: 13%">Appréciation</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Grouper les notes par matière
                    $notesByMatiere = $bulletin->notes->groupBy('matiere_id');
                    $totalMoyenneCoef = 0;
                    $totalCoef = 0;
                @endphp

                @foreach($notesByMatiere as $matiereId => $notesMatiere)
                    @php
                        $matiere = $notesMatiere->first()->matiere;
                        $coefficient = $notesMatiere->first()->coefficient;
                        
                        // MÉTHODE SIMPLE : Prendre les 2 premières notes disponibles
                        // Si vous avez 2+ notes par matière, elles seront considérées comme Devoir et Examen
                        $allNotes = $notesMatiere->values();
                        
                        $valeurDevoir = $allNotes->count() > 0 ? $allNotes->get(0)->valeur : null;
                        $valeurExamen = $allNotes->count() > 1 ? $allNotes->get(1)->valeur : null;
                        
                        // Calcul de la moyenne de la matière
                        $moyenneMatiere = null;
                        if ($valeurDevoir !== null && $valeurExamen !== null) {
                            $moyenneMatiere = ($valeurDevoir + $valeurExamen) / 2;
                        } elseif ($valeurDevoir !== null) {
                            $moyenneMatiere = $valeurDevoir;
                        } elseif ($valeurExamen !== null) {
                            $moyenneMatiere = $valeurExamen;
                        }
                        
                        // Moyenne avec coefficient
                        $moyenneCoef = $moyenneMatiere ? $moyenneMatiere * $coefficient : null;
                        
                        if ($moyenneCoef !== null) {
                            $totalMoyenneCoef += $moyenneCoef;
                            $totalCoef += $coefficient;
                        }
                        
                        // Appréciation
                        $appreciation = '';
                        if ($moyenneMatiere !== null) {
                            if ($moyenneMatiere >= 16) $appreciation = 'Excellent';
                            elseif ($moyenneMatiere >= 14) $appreciation = 'Très Bien';
                            elseif ($moyenneMatiere >= 12) $appreciation = 'Bien';
                            elseif ($moyenneMatiere >= 10) $appreciation = 'Assez Bien';
                            else $appreciation = 'Insuffisant';
                        }
                    @endphp
                    <tr>
                        <td class="matiere">{{ $matiere->libelle }}</td>
                        
                        {{-- Note Devoir --}}
                        <td class="note {{ $valeurDevoir !== null ? ($valeurDevoir >= 10 ? 'pass' : 'fail') : 'empty' }}">
                            {{ $valeurDevoir !== null ? number_format($valeurDevoir, 2) : '-' }}
                        </td>
                        
                        {{-- Note Examen --}}
                        <td class="note {{ $valeurExamen !== null ? ($valeurExamen >= 10 ? 'pass' : 'fail') : 'empty' }}">
                            {{ $valeurExamen !== null ? number_format($valeurExamen, 2) : '-' }}
                        </td>
                        
                        {{-- Coefficient --}}
                        <td class="coefficient">{{ $coefficient }}</td>
                        
                        {{-- Moyenne Matière --}}
                        <td class="moyenne-matiere {{ $moyenneMatiere !== null ? ($moyenneMatiere >= 10 ? 'pass' : 'fail') : 'empty' }}">
                            {{ $moyenneMatiere !== null ? number_format($moyenneMatiere, 2) : '-' }}
                        </td>
                        
                        {{-- Moyenne avec Coefficient --}}
                        <td class="note {{ $moyenneCoef !== null ? ($moyenneMatiere >= 10 ? 'pass' : 'fail') : 'empty' }}">
                            {{ $moyenneCoef !== null ? number_format($moyenneCoef, 2) : '-' }}
                        </td>
                        
                        {{-- Appréciation --}}
                        <td style="text-align: center; font-size: 9pt; font-style: italic;">
                            {{ $appreciation }}
                        </td>
                    </tr>
                @endforeach

                {{-- Ligne de total --}}
                <tr style="background-color: #1F4788; color: white; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</td>
                    <td class="coefficient">{{ $totalCoef }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        {{-- Résultats et appréciation --}}
        <div class="resultats">
            <div class="appreciation-box">
                <h3>Appréciation générale :</h3>
                <p>{{ $bulletin->appreciation }}</p>
            </div>
            
            <div class="moyenne-box">
                <h3>MOYENNE GÉNÉRALE</h3>
                <div class="moyenne">
                    {{ $totalCoef > 0 ? number_format($totalMoyenneCoef / $totalCoef, 2) : '0.00' }}/20
                </div>
            </div>
        </div>

        {{-- Footer avec signatures et QR Code --}}
        {{-- <div class="footer-section">
            
            <div class="qr-section">
                <h4>QR Code de Vérification</h4>
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)
                    ->backgroundColor(255, 255, 255)
                    ->color(31, 71, 136)
                    ->errorCorrection('H')
                    ->generate(json_encode([
                        'matricule' => $bulletin->eleve->matricule,
                        'nom' => $bulletin->eleve->nom,
                        'prenom' => $bulletin->eleve->prenom ?? '',
                        'classe' => $bulletin->classe->nom_classe,
                        'id' => $bulletin->id,
                        'moyenne' => $totalCoef > 0 ? number_format($totalMoyenneCoef / $totalCoef, 2) : '0.00',
                        'periode' => $bulletin->periode,
                        'annee' => $bulletin->annee->libelle
                    ])) 
                !!}
                <div class="qr-info">Scanner pour vérifier<br>ID: {{ $bulletin->id }}</div>
            </div> --}}


            {{-- Signature Directeur --}}
            <div class="signature-section">
                <h4>Le Directeur Général</h4>
                <div class="signature-line"></div>
            </div>
        </div>

        {{-- Footer document --}}
        <div class="document-footer">
            Bulletin généré le {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm') }}
        </div>
    </div>
</body>
</html>