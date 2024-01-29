<?php

namespace TrieNamespace;

class TrieNode {
    public $children = array();
    public $isEndOfWord = false;
}

class Trie {
    private $root;

    public function __construct() {
        $this->root = new TrieNode();
    }

    public function insert($word) {
        $node = $this->root;
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            if (!isset($node->children[$char])) {
                $node->children[$char] = new TrieNode();
            }
            $node = $node->children[$char];
        }
        $node->isEndOfWord = true;
    }

    public function search($prefix) {
        $node = $this->root;
        $result = array();
        for ($i = 0; $i < strlen($prefix); $i++) {
            $char = $prefix[$i];
            if (!isset($node->children[$char])) {
                return $result;
            }
            $node = $node->children[$char];
        }
        $this->findAllWords($node, $prefix, $result);
        return $result;
    }

    private function findAllWords($node, $prefix, &$result) {
        if ($node->isEndOfWord) {
            $result[] = $prefix;
        }
        foreach ($node->children as $char => $childNode) {
            $this->findAllWords($childNode, $prefix . $char, $result);
        }
    }
}

?>
