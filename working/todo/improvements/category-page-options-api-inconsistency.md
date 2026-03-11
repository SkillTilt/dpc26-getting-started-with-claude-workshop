# Proposal: Migrate CategoryPage.vue from Options API to Composition API

## Current State

`frontend/src/pages/CategoryPage.vue` uses the Vue Options API (`export default { data(), methods: {}, mounted() {} }`) while every other component in the frontend uses the Composition API with `<script setup>`.

This inconsistency means:

- Developers context-switching into this file need to mentally shift API styles
- Shared composables (`useApi`, `useAuth`) are used with the Composition API pattern everywhere else, but this component accesses them differently
- Code review and linting rules can't assume a single component style

## Proposal

Rewrite `CategoryPage.vue` to use `<script setup>` with the Composition API, matching the pattern established in all other components.

## Suggested Approach

1. Convert `data()` properties to `ref()` / `reactive()`
2. Convert `methods` to plain functions
3. Convert `mounted()` to `onMounted()`
4. Convert `computed` properties to `computed()`
5. Use `useRoute()` from vue-router instead of `this.$route`
6. Verify the template still works (template syntax is the same between both APIs)

## Scope

Small -- this is a single file change with no impact on other components. Good candidate for a quick cleanup task.
