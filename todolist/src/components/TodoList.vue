<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue'
import type { Ref } from 'vue'
import ListItem from './ListItem.vue'

type Item = { id: number; title: string; checked?: boolean }

const storageItems: Ref<Item[]> = ref([])

// Función para obtener los elementos desde la API
const fetchItemsFromApi = async (): Promise<void> => {
  try {
    const res = await fetch('http://localhost:3000/items')
    const data = await res.json()
    if (data.length === 0) {
      // Si la API no tiene elementos, inicializamos con los valores por defecto
      const defaultItems: Omit<Item, 'id'>[] = [
        { title: 'Make a todo list app', checked: true },
        { title: 'Predict the weather', checked: false },
        { title: 'Read some comics', checked: false },
        { title: "Let's get cooking", checked: false },
        { title: 'Pump some iron', checked: false },
        { title: 'Track my expenses', checked: false },
        { title: 'Organise a game night', checked: false },
        { title: 'Learn a new language', checked: false },
        { title: 'Publish my work' }
      ]
      // Guardamos cada item en la API usando POST y actualizamos el estado local
      for (const item of defaultItems) {
        const savedItem = await saveItemJson(item)
        storageItems.value.push(savedItem)
      }
    } else {
      storageItems.value = data
    }
  } catch (error) {
    console.error("Error al obtener los items desde la API:", error)
  }
}

// Computado para ordenar la lista (los items completados al final)
const sortedList = computed(() =>
  [...storageItems.value].sort(
    (a, b) => (a.checked ? 1 : 0) - (b.checked ? 1 : 0)
  )
)

// Función para actualizar un item (toggle checked) y persistirlo en la API
const updateItem = async (item: Item): Promise<void> => {
  const updatedItem = storageItems.value.find((i) => i.id === item.id)
  if (updatedItem) {
    updatedItem.checked = !updatedItem.checked
    const newItem = await updateItemJson(updatedItem)
    // Actualizamos el item en el estado local para reflejar el cambio
    const index = storageItems.value.findIndex((i) => i.id === newItem.id)
    if (index !== -1) {
      storageItems.value[index] = newItem
    }
  }
}

// Función para guardar un item nuevo en la API (POST)
const saveItemJson = async (item: Omit<Item, 'id'>): Promise<Item> => {
  try {
    const response = await fetch('http://localhost:3000/items', {
      method: 'POST',
      body: JSON.stringify(item),
      headers: {
        'Content-type': 'application/json; charset=UTF-8'
      }
    })
    const json = await response.json()
    console.log("Guardado:", json)
    return json as Item
  } catch (error) {
    console.error("Error al guardar el item:", error)
    throw error
  }
}

// Función para actualizar un item existente en la API (PUT)
const updateItemJson = async (item: Item): Promise<Item> => {
  try {
    const response = await fetch(`http://localhost:3000/items/${item.id}`, {
      method: 'PUT',
      body: JSON.stringify(item),
      headers: {
        'Content-type': 'application/json; charset=UTF-8'
      }
    })
    const json = await response.json()
    console.log("Actualizado:", json)
    return json as Item
  } catch (error) {
    console.error("Error al actualizar el item:", error)
    throw error
  }
}

// Al montar el componente, obtenemos los datos desde la API
onMounted(() => {
  fetchItemsFromApi()
})
</script>

<template>
  <ul>
    <!-- Se utiliza el id único de cada item para la key -->
    <li v-for="item in sortedList" :key="item.id">
      <ListItem :is-checked="item.checked" @click="updateItem(item)">
        {{ item.title }}
      </ListItem>
    </li>
  </ul>
</template>

<style scoped>
ul {
  list-style: none;
}

li {
  margin: 0.4rem 0;
}
</style>
