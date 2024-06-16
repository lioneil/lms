<?php

namespace Course\Enumerations;

abstract class LessonMetadataKeys
{
    const PATHNAME = 'pathname';
    const INTERACTIVEPATH = 'interactivepath';
    const ARCHIVEPATH = 'archivepath';

    const ASSIGNMENT_KEY = 'assignment';
    const AUDIO_KEY = 'audio';
    const CONTENT_KEY = 'content';
    const EDITOR_KEY = 'editor';
    const EMBED_KEY = 'embed';
    const EXAM_KEY = 'exam';
    const INTERACTIVE_KEY = 'interactive';
    const PDF_KEY = 'pdf';
    const PRESENTATION_KEY = 'presentation';
    const SCORM_KEY = 'scorm';
    const SECTION_KEY = 'section';
    const TEXT_KEY = 'text';
    const VIDEO_KEY = 'video';

    /**
     * Return the array of playable contents.
     *
     * @return array
     */
    public static function nonPlayables(): array
    {
        return [
            self::SECTION_KEY,
        ];
    }

    /**
     * Return the array of playable contents.
     *
     * @return array
     */
    public static function playables(): array
    {
        return [
            self::EDITOR_KEY,
            self::ASSIGNMENT_KEY,
            self::CONTENT_KEY,
            self::EMBED_KEY,
            self::EXAM_KEY,
            self::PDF_KEY,
            self::PRESENTATION_KEY,
            self::TEXT_KEY,
            self::SCORM_KEY,
            self::INTERACTIVE_KEY,
            self::VIDEO_KEY,
            self::AUDIO_KEY,
        ];
    }
}
