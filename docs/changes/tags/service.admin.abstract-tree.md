# Изменения: service.admin.abstract-tree

- 2026-04-29 — создан `AbstractTreeService` с общей логикой дерева (buildGroupedTree, allNodesOrderedForMeta, saveOrder, deleteNodeReparentingChildren) → [../../diary/2026/04/2026-04-29-kiss-refactoring.md#01JKISS20260429TREE]
- 2026-05-10 — warning при глубине > 3; try/catch + `Log::error` / `Log::critical` на операциях дерева → [../../diary/2026/05/2026-05-10-development.md#01JTREELOG20260510]
- 2026-05-10 — порог глубины для warning задаётся `maxRecommendedTreeDepth()` в подклассе → [../../diary/2026/05/2026-05-10-development.md#01JTREEDEPTHLIM20260510]
