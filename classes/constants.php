<?php
namespace local_dta;
class CONSTANTS{
    // TYPES OF REACTIONS
    const REACTIONS_INSTANCES = [
        "CASE" => 0,
        "EXPERIENCE" => 1
    ];
    // GROUPS IN REFLECTION
    const GROUPS = [
        "SO_WHAT_HOW" => 1,
        "NOW_WHAT_ACTION" => 2,
        "NOW_WHAT_REFLECTION" => 3,
        "EXTRA" => 6 
    ]; 
    // TYPES OF SECTIONS OF REFLECTION 
    // RIGHT NOW ONLY TEXT IS IMPLEMENTED
    const SECTION_TYPES = [
        "TEXT" => [
            "ID" => 1,
            "TABLE" => "digital_refl_sec_text",
        ]
    ];
    // TYPES OF FILES 
    const FILE_TYPES = [
        "IMAGE" => [
            "ID" => 1,
            "FOLDER" => "images"
        ],
        "VIDEO" => [
            "ID" => 2,
            "FOLDER" => "videos"
        ],
        "AUDIO" => [
            "ID" => 3,
            "FOLDER" => "audios"
        ],
        "DOCUMENT" => [
            "ID" => 4,
            "FOLDER" => "documents"
        ]
    ];
    // TYPES OF THEMES_CONTEXT
    const THEMES_CONTEXT = [
        "EXPERIENCE" => 1,
        "CASE" => 2,
        "RESOURCE" => 3
    ];


}