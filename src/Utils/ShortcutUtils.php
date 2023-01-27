<?php

namespace App\Utils;

class ShortcutUtils
{
    /**
     * @param array $data
     * @param array $shortcutDatas
     * @param string $projectId
     * @param array $mapping
     * @return array
     */
    public static function generatePostDataStories(
        array  $data,
        array  $shortcutDatas,
        string $projectId,
        array  $mapping
    ): array
    {
        return array_map(function ($row) use ($shortcutDatas, $projectId, $mapping) {
            $row = array_combine($mapping, $row);

            foreach ($row as $key => $value) {
                foreach ($shortcutDatas as $shortcutData) {
                    $row[$key] = self::replaceNameById($key, $value, $row, $shortcutData);
                }
            }

            $row['project_id'] = $projectId;

            return $row;
        }, $data);
    }

    /**
     * @param string $key
     * @param string $value
     * @param array $row
     * @param array $data
     * @return string
     */
    public static function replaceNameById(string $key, string $value, array $row, array $data): string
    {
        // If the value exists on the application, returns the corresponding id, otherwise returns its own value
        if (in_array($value, array_keys($data))) {
            return $data[$value];
        }

        return $row[$key];
    }

    /**
     * @param array $stories
     * @return array
     */
    public static function generateStoryLinks(array $stories): array
    {
        $storyLinks = [];

        foreach ($stories as $story) {
            // If the story is not blocked by other stories
            if (empty($story['blocked_by'])) continue;

            // Find parent stories
            $parents = array_filter($stories, function ($filterStory) use ($story) {
                return $filterStory['name'] === $story['blocked_by'];
            });

            // Prepare parent story array
            $storyLinksParents = [];
            foreach ($parents as $parent) {
                $storyLinksParents[]['id'] = $parent['id'];
            }

            $storyLinks[] = [
                'id' => $story['id'],
                'parents' => $storyLinksParents,
            ];
        }

        return $storyLinks;
    }
}
