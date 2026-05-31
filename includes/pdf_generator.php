<?php
require_once __DIR__ . '/../vendor/fpdf/fpdf.php';

class CovenantPDF extends FPDF
{
    public $eventVenue = "DNSC GAD Conference Room and Via MS Teams";
    public $eventDate = "May 4, 2026 8:00-1:00 PM";
    public $eventTitle = "The Covenant of Commitment";

    // Page header
    function Header()
    {
        // Set standard margins for the header
        $this->SetMargins(20, 20, 20);

        // Logos
        $sourceDnsc = __DIR__ . '/../assets/images/dnsclogo.png';
        if (file_exists($sourceDnsc)) {
            $sanitizedDnsc = $this->getCleanImagePath($sourceDnsc, 'dnsc_header');
            $this->Image($sanitizedDnsc, 18, 15, 20);
        }

        $sourceIc = __DIR__ . '/../assets/images/iclogo.png';
        if (file_exists($sourceIc)) {
            $sanitizedIc = $this->getCleanImagePath($sourceIc, 'ic_header');
            $this->Image($sanitizedIc, 172, 16, 20);
        }

        $this->SetY(15);
        $this->SetFont('Arial', 'B', 10);
        $this->SetX(40);
        $this->MultiCell(130, 5, $this->eventTitle, 0, 'C');

        $this->SetX(40);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(130, 4, "Venue: " . $this->eventVenue . "\nDate: " . $this->eventDate, 0, 'C');

        $this->Ln(8);

        // Only show the document title on PAGE 1
        if ($this->PageNo() === 1) {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'The Covenant of Commitment', 0, 1, 'C');
            $this->Ln(2);
        } else {
            $this->Ln(4); // smaller spacing on subsequent pages
        }
    }

    /**
     * Helper to clean PNGs that cause distortion in FPDF 
     * Converts to high quality JPEG on the fly
     */
    public function getCleanImagePath(string $path, string $prefix): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== 'png')
            return $path; // Only needed fix for PNGs

        $tmpPath = __DIR__ . '/../assets/signatures/tmp_' . $prefix . '.jpg';

        // Skip if recently generated to save CPU
        if (file_exists($tmpPath) && (time() - filemtime($tmpPath) < 3600)) {
            return $tmpPath;
        }

        if (function_exists('imagecreatefrompng')) {
            $img = @imagecreatefrompng($path);
            if ($img) {
                $w = imagesx($img);
                $h = imagesy($img);
                $out = imagecreatetruecolor($w, $h);
                $white = imagecolorallocate($out, 255, 255, 255);
                imagefill($out, 0, 0, $white);
                imagecopy($out, $img, 0, 0, 0, 0, $w, $h);
                imagejpeg($out, $tmpPath, 95);
                imagedestroy($img);
                imagedestroy($out);
                return $tmpPath;
            }
        }
        return $path;
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }
}

function generateCovenantPDF(array $submission_data, string $signature_path, string $output_filename, ?array $activeEvent = null)
{
    $pdf = new CovenantPDF();
    if ($activeEvent) {
        $pdf->eventVenue = $activeEvent['venue'];
        $pdf->eventDate = $activeEvent['event_date'];
        $pdf->eventTitle = $activeEvent['title'];
    }
    $pdf->AliasNbPages();
    // 20mm margins for a superior printed look (Left, Top, Right)
    $pdf->SetMargins(20, 20, 20);
    $pdf->AddPage();

    // Set Font for Body
    $pdf->SetFont('Arial', '', 10);

    // Paragraph 1
    $text1 = "We, the undersigned representatives of industry, academia, and partner institutions, affirm our shared commitment to strengthening Information Technology Education (ITE) in the Davao del Norte State College - Institute of Computing. Recognizing the rapid evolution of digital technologies and the increasing demand for a future-ready workforce, we pledge to work collaboratively to align academic preparation with industry needs.";
    $pdf->MultiCell(0, 6, $text1);
    $pdf->Ln(3);

    $text2 = "Through this Covenant of Commitment, we express our support in the DNSC STRATEGIC PARTNERSHIPS FOR RESEARCH, INTERNSHIPS, AND NEXT-GEN TECHNOLOGY IN INFORMATION TECHNOLOGY (DNSC SPRINT-IT) and our participation in the DNSC INSTITUTE OF COMPUTING INDUSTRY ADVISORY COUNCIL MEETINGS, established to guide curriculum enhancement, practicum development, and industry-academe collaboration in the region.";
    $pdf->MultiCell(0, 6, $text2);
    $pdf->Ln(4);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Together, we commit to:', 0, 1);
    $pdf->SetFont('Arial', '', 10);

    // List 1
    $list1 = [
        "1. Promote Industry-Academe Alignment: Provide insights on emerging technologies, industry practices, and workforce demands to help ensure that IT education programs remain relevant and responsive to evolving industry needs.",
        "2. Support Curriculum and Practicum Development: Contribute recommendations and feedback to improve IT curricula and practicum frameworks, including internship and on-the-job training (OJT) programs.",
        "3. Enhance Experiential Learning Opportunities: Facilitate internships, OJT placements, mentorship, and real-world project exposure that strengthen students' practical competencies and professional readiness.",
        "4. Participate in Advisory and Consultative Processes: Engage in consultations, meetings, and collaborative initiatives of DNSC SPRINT-IT and partner Higher Education Institutions (HEIs) to support continuous improvement in IT education.",
        "5. Encourage Faculty Development and Industry Exposure: Provide opportunities for faculty immersion, training, or industry engagement to ensure that teaching practices remain aligned with current technological trends and professional practices.",
        "6. Promote Innovation and Collaborative Research: Support partnerships in research, development, and innovation initiatives that contribute to technological advancement and regional economic growth."
    ];

    foreach ($list1 as $item) {
        $pdf->SetX(25);
        $pdf->MultiCell(160, 6, $item);
        $pdf->Ln(1);
    }

    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Responsibilities as Members of Covenant', 0, 1);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 5, 'As industry partners and members of the Regional Advisory Council, we commit to:', 0, 1);
    $pdf->SetFont('Arial', '', 10);

    // List 2
    $list2 = [
        "- Institutional Advisory Support - Participate in consultative meetings and industry dialogues. Serve as members of DNSC Institute of Computing Institutional Industry Advisory Councils of Davao del Norte State College",
        "- Curriculum Input - Provide technical expertise and recommendations to ensure that IT education programs remain aligned with industry standards and emerging technologies.",
        "- Policy and Program Consultation - Offer expert insights in the development and review of regional initiatives, policies, and programs related to IT education.",
        "- Faculty Immersion Opportunities - Support opportunities for faculty members to gain relevant industry exposure.",
        "- Internship and OJT Placement - Provide internship or on-the-job training opportunities and mentorship for IT students.",
        "- Research and Development Collaboration - Engage in collaborative research, innovation initiatives, or technology development projects with partner institutions."
    ];

    foreach ($list2 as $item) {
        $pdf->SetX(25);
        $pdf->MultiCell(160, 6, $item);
        $pdf->Ln(1);
    }

    $pdf->Ln(5);
    $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
    $pdf->Ln(5);

    $manifest = "Through this covenant, we reaffirm our shared responsibility to cultivate a dynamic, industry-responsive IT education ecosystem that prepares graduates with the skills, knowledge, and values necessary to thrive in the digital economy.";
    $pdf->MultiCell(0, 6, $manifest);

    $pdf->Ln(4);
    $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
    $pdf->Ln(8);

    // ---------------------------------------------------------
    // PAGE 2 (Or continue if space allows, but let's just make it flow)
    // ---------------------------------------------------------

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, 'Partner Information', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5, 'With Data Privacy Agreement', 0, 1);
    $pdf->Ln(5);

    // Table Draw
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 8, 'Field', 1, 0, 'C');
    $pdf->Cell(120, 8, 'Details', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 10);

    // Row 1
    $pdf->Cell(50, 8, 'Organization Name', 1);
    $pdf->Cell(120, 8, ' ' . $submission_data['organization_name'], 1, 1);

    // Row 2
    $pdf->Cell(50, 8, 'Type of Institution', 1);
    $pdf->Cell(120, 8, ' ' . $submission_data['institution_type'], 1, 1);

    // Row 3
    $pdf->Cell(50, 8, 'Represented By', 1);
    $pdf->Cell(120, 8, ' ' . $submission_data['represented_by'], 1, 1);

    // Row 4
    $pdf->Cell(50, 8, 'Position/Title', 1);
    $pdf->Cell(120, 8, ' ' . $submission_data['position_title'], 1, 1);

    // Row 5
    $pdf->Cell(50, 8, 'Email Address', 1);
    $pdf->Cell(120, 8, ' ' . $submission_data['email_address'], 1, 1);

    // Row 6
    $pdf->Cell(50, 8, 'Contact Number', 1);
    $pdf->Cell(120, 8, ' ' . $submission_data['contact_number'], 1, 1);

    $pdf->Ln(20); 

    // Calculate Name Width for Perfect Line Length - STRICT MATCHING
    $represented_name = strtoupper($submission_data['represented_by']);
    $pdf->SetFont('Arial', 'B', 10);
    $nameWidth = $pdf->GetStringWidth($represented_name);
    $lineWidth = $nameWidth; 
    $startX = 20;

    // Stamping the Digital Signature
    // Check if signature image exists
    if (file_exists($signature_path)) {
        // CLEAN THE SIGNATURE: signaturePad PNGs often have alpha/interlacing that FPDF hates
        $cleanSignaturePath = $pdf->getCleanImagePath($signature_path, 'sig_' . time());
        
        // Get image dimensions to maintain aspect ratio and prevent massive vertical bleed
        list($imgWidth, $imgHeight) = getimagesize($cleanSignaturePath);
        $ratio = $imgWidth / $imgHeight;

        // Enforce minimum 50mm width for readability on short names
        $targetWidth = max($lineWidth * 1.5, 50); 
        $targetHeight = $targetWidth / $ratio;

        // Constrain max height to 25mm to avoid covering text below
        $maxHeight = 25;
        if ($targetHeight > $maxHeight) {
            $targetHeight = $maxHeight;
            $targetWidth = $targetHeight * $ratio;
        }

        // Center over the line
        $sigX = $startX + ($lineWidth / 2) - ($targetWidth / 2);
        $currY = $pdf->GetY();
        
        // Position the center of the image slightly above the line to account for canvas padding
        $sigY = $currY - ($targetHeight / 2) - 5;
        $pdf->Image($cleanSignaturePath, $sigX, $sigY, $targetWidth, $targetHeight);
    }

    // Signature Line - Matches name length EXACTLY
    $pdf->Line($startX, $pdf->GetY(), $startX + $lineWidth, $pdf->GetY());

    $pdf->SetFont('Arial', 'B', 10);
    // Use the Represented By name as the printed name over the line
    $pdf->Cell($lineWidth, 5, $represented_name, 0, 1, 'L');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell($lineWidth, 5, 'Date Signed: ' . date('M d, Y', strtotime($submission_data['signed_at'] ?? 'now')), 0, 1, 'L');

    // Save to server
    $pdf->Output('F', $output_filename);
    return true;
}
?>


