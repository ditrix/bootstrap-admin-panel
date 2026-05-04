# Изменения: service.admin.abstract-tree

- 2026-04-29 — создан `AbstractTreeService` с общей логикой дерева (buildGroupedTree, allNodesOrderedForMeta, saveOrder, deleteNodeReparentingChildren) → [../../diary/2026/04/2026-04-29-kiss-refactoring.md#01JKISS20260429TREE]
- 2026-05-03 — `createItem()` и `buildParentOptionsHtml()` подняты из подклассов в `AbstractTreeService` (были идентичны в CategoryTree/MainMenu сервисах) → [../../diary/2026/05/2026-05-03-tree-refactoring-aeb.md#01JTREE20260503AEB]
