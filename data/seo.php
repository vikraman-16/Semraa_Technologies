<?php
$pages = [
 'home'=>['Software Company near Tenkasi | Semraa Technologies','Based near Tenkasi, Semraa Technologies builds websites and custom business software for Tenkasi, Tirunelveli and nearby areas. Discuss your project.',''],
 'about'=>['About Semraa Technologies | IT Company near Tenkasi','Meet the approach behind Semraa Technologies near Tenkasi. Explore our values, capabilities and practical process for designing and delivering software.','about'],
 'services'=>['IT & Software Services near Tenkasi | Semraa Technologies','Explore web development, custom software, cloud, cybersecurity, UI/UX design and IT consulting near Tenkasi. Discuss your next project with Semraa today.','services'],
 'products'=>['Business Software Products | Semraa Technologies India','Explore Semraa Nexus DB and Work Pulse. Discuss business data, reporting and team workflows in a tailored product demo with Semraa Technologies in India.','products'],
 'portfolio'=>['Project Concepts & Capabilities | Semraa Technologies','Explore illustrative web, software, cloud and design project concepts from Semraa Technologies. Discuss how a similar approach could fit your organisation.','portfolio'],
 'blog'=>['Software & Business Technology Blog | Semraa Technologies','Read practical articles on websites, software planning, cloud, design and security from Semraa Technologies. Find useful starting points for your business.','blog'],
 'contact'=>['Contact Semraa Technologies | Tenkasi Area IT Services','Contact Semraa Technologies near Tenkasi to discuss websites, custom software, cloud or security. Share your requirements and start a useful conversation.','contact'],
 'book-demo'=>['Book a Software & IT Demo | Semraa Technologies India','Request a tailored service or product demo from Semraa Technologies. Share your requirements, choose a preferred meeting time and discuss your next steps.','book-demo'],
 'consultation'=>['Book a Consultation | Semraa Technologies','Choose a published consultation slot to discuss your website, software or IT project with Semraa Technologies. Availability is managed by our team.','consultation'],
 'booking-success'=>['Demo Request Received | Semraa Technologies Tenkasi','Review your demo request and next steps. A preferred meeting time is confirmed separately by the Semraa Technologies team after reviewing your requirements.','booking-success'],
 'privacy'=>['Privacy Policy & Website Enquiries | Semraa Technologies','Learn how the Semraa Technologies website handles contact and demo enquiries, session cookies and submitted information, and how to raise a privacy request.','privacy'],
 'terms'=>['Website Terms & Demo Requests | Semraa Technologies','Read the terms for using the Semraa Technologies website, submitting enquiries and requesting demos. Project scope and commercial terms are agreed separately.','terms'],
 '404'=>['Page Not Found | Semraa Technologies near Tenkasi','The page you requested could not be found. Visit Semraa Technologies services, browse the blog or get in touch to discuss your software project requirements.','404'],
 'admin'=>['Administration | Semraa Technologies','Manage submitted demo enquiries, review requirements and update request status. This private administration area is restricted to authorised Semraa personnel.','admin'],
];
foreach (require __DIR__ . '/services.php' as $slug=>$item) {
 $pages['service:' . $slug] = [$item['name'] . ' near Tenkasi | Semraa Technologies', 'Explore ' . strtolower($item['name']) . ' with Semraa Technologies near Tenkasi. Understand the approach, benefits and next steps, then request a tailored discussion.', 'services/' . $slug];
}
foreach (require __DIR__ . '/locations.php' as $slug=>$item) {
 $pages['location:' . $slug] = ['Software Company Serving ' . $item['name'] . ' | Semraa Technologies', 'Explore websites, custom software and IT services for ' . $item['name'] . ' businesses. Discuss your requirements with the Tenkasi-area Semraa Technologies team.', 'locations/' . $slug];
}
foreach (require __DIR__ . '/blog.php' as $slug=>$item) {
 $pages['post:' . $slug] = [$item['title'] . ' | Semraa', $item['excerpt'] . ' Read the practical guide from Semraa Technologies.', 'blog/' . $slug];
}
$pages['location:tirunelveli'][0] = 'Software & IT Services in Tirunelveli | Semraa';
$pages['location:tenkasi'][0] = 'Software & Web Development for Tenkasi | Semraa';
$pages['location:tenkasi'][1] = 'Website development, custom software and IT consulting for Tenkasi businesses from Tenkasi-area Semraa Technologies. Discuss your project requirements.';

// Match each service to a distinct customer need, without repeating generic snippets.
$serviceMetadata = [
 'web-development'=>['Web Development near Tenkasi | Semraa Technologies','Responsive business websites with clear service pages, accessible enquiry forms and technical SEO foundations. Discuss your website with Semraa near Tenkasi.'],
 'software-development'=>['Custom Software Development near Tenkasi | Semraa','Connect requests, approvals and reporting with custom business software. Semraa near Tenkasi helps you define, build and hand over a practical first release.'],
 'cloud-devops'=>['Cloud & DevOps Services near Tenkasi | Semraa','Plan cloud infrastructure, repeatable deployments and recovery procedures with Semraa. Explore cloud and DevOps support for your business near Tenkasi.'],
 'cybersecurity'=>['Cybersecurity Services near Tenkasi | Semraa','Explore scoped security assessments, VAPT and remediation planning with Semraa near Tenkasi. Start with your systems, risks and assessment requirements.'],
 'ui-ux-design'=>['UI/UX Design near Tenkasi | Semraa Technologies','Make digital tasks easier with user research, interface design and usable prototypes. Explore Semraa’s UI/UX design approach for websites and business software.'],
 'it-consulting'=>['IT Consulting near Tenkasi | Semraa Technologies','Compare technology options, prioritise improvements and plan delivery with Semraa’s IT consulting. Get a practical starting point for your business requirements.'],
];
foreach ($serviceMetadata as $slug=>$metadata) {
 $pages['service:' . $slug][0] = $metadata[0];
 $pages['service:' . $slug][1] = $metadata[1];
}
$locationDescriptions = [
 'tirunelveli'=>'Tenkasi-area Semraa Technologies builds websites and custom software around business workflows. Explore our local approach, services and project discussions.',
 'palayamkottai'=>'Websites, enquiry journeys and internal applications for Palayamkottai organisations. Work with Tenkasi-area Semraa on a clearly scoped digital project.',
 'nagercoil'=>'Connect your website enquiries with organised delivery. Semraa serves Nagercoil businesses with web development, software and planned remote collaboration.',
 'thoothukudi'=>'Explore operational software, integrations and IT services for Thoothukudi businesses. Tenkasi-area Semraa plans solutions around your information workflows.',
 'madurai'=>'Review business software, customer portals and technology choices with Semraa. Serving Madurai through planned projects and remote reviews from the Tenkasi area.',
];
foreach ($locationDescriptions as $slug=>$description) { $pages['location:' . $slug][1] = $description; }
$pages['products'][0] = 'Business Software Concepts | Semraa Nexus DB & Work Pulse';
$pages['products'][1] = 'Explore Semraa Nexus DB and Work Pulse product concepts for business data and team workflows. Features, availability and scope are confirmed during a discussion.';
$postMetadata = [
 'why-tirunelveli-businesses-need-a-website-2026'=>['Why Tirunelveli Businesses Need a Website | Semraa 2026','Discover what a useful website can do for your Tirunelveli business in 2026, from clear service information to mobile enquiries and measurable improvements.'],
 'vapt-explained-for-small-businesses'=>['VAPT Explained for Small Businesses | Semraa Technologies','Understand VAPT scope, written authorisation, useful security reports and remediation planning. A practical introduction for small businesses from Semraa.'],
 'choose-software-company-tirunelveli'=>['How to Choose a Software Company in Tirunelveli | Semraa','Compare software companies in Tirunelveli with questions about project scope, ownership, delivery reviews and handover. Read this practical guide by Semraa.'],
 'cloud-migration-readiness-checklist'=>['Cloud Migration Readiness Checklist | Semraa Technologies','Prepare for a cloud migration by mapping dependencies, checking operating needs, rehearsing data transfers and planning recovery. Read the guide from Semraa.'],
 'design-better-enquiry-forms'=>['Better Enquiry Forms: A Practical Design Guide | Semraa','Design enquiry forms with useful questions, relevant fields, clear validation and helpful confirmations. Explore practical improvements for your business.'],
];
foreach ($postMetadata as $slug=>$metadata) { $pages['post:' . $slug][0]=$metadata[0]; $pages['post:' . $slug][1]=$metadata[1]; }
foreach (require __DIR__ . '/demo_pages.php' as $slug=>$demoPage) { $pages['demo:'.$slug] = [$demoPage['title'],$demoPage['description'],'book-demo/'.$slug]; }
return array_map(static fn($p)=>['title'=>$p[0],'description'=>$p[1],'path'=>$p[2]], $pages);
