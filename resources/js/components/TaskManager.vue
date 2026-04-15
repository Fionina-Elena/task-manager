<template>
  <div class="TaskManager">
    <h3 style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
      <b>Список задач</b>
    </h3>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
      <button class="btn-addTask" @click="showModal('create')" type="button">Добавить задачу
        <i class="fa-solid fa-plus" style="color: #e3e3e3;"></i></button>
    </div>
  </div>

  <table class="table table-striped table-hover mx-auto"
    style="max-width: 1400px; padding: 0 2rem; font-family: 'Times New Roman', Times, serif;">
    <thead class="text-center">
      <th scope="col">Id</th>
      <th scope="col">Название</th>
      <th scope="col">Описание</th>
      <th scope="col">Дата</th>
      <th scope="col">Время</th>
      <th scope="col">Статус</th>
      <th scope="col">Дата добавления</th>
      <th scope="col">Дата редактирования</th>
      <th scope="col">Действия </th>
    </thead>
    <tbody class="text-center">
      <tr v-for="task in tasks" :key="task.id">
        <td>{{ task.id }}</td>
        <td>{{ task.title }}</td>
        <td>{{ task.description }}</td>
        <td>{{ task.date }}</td>
        <td>{{ task.time }}</td>
        <td :class="{
          'text-success': task.task_status === 0,
          'text-primary': task.task_status === 1,
        }">{{ statusTask(task.task_status) }}
        </td>
        <td>{{ task.created_at }}</td>
        <td>{{ task.updated_at }}</td>
        <td>
          <div class="btn-group" role="group" aria-label="Basic example">
            <button v-if="task.task_status === 0 || task.task_status === 1" @click="showModal('update', task)"
              class="btn btn-warning btn-sm">
              Редактировать</button>

            <button v-if="task.task_status === 0" @click="completed(task.id)" class="btn btn-primary btn-sm">
              Выполнить</button>

            <button v-if="task.task_status === 1" @click="atWork(task.id)" class="btn btn-success btn-sm">
              Вернуть в работу</button>

            <button v-if="task.task_status === 0 || task.task_status === 1" @click="showModal('destroy', task)"
              class="btn btn-danger btn-sm">
              Удалить</button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>

  <MDBModal id="createModal" tabindex="-1" labelledby="createModalLabel" v-model="createModal">
    <MDBModalHeader>
      <MDBModalTitle id="createModalLabel"> Необходимо указать следующие данные: </MDBModalTitle>
    </MDBModalHeader>
    <MDBModalBody>

      <div v-if="errors" class="alert" style="color:red">
        <b> Проверьте корректность введённых данных: </b>
        <i class="fa-solid fa-arrow-turn-down" style="color: #ed1212;"></i>
      </div>

      <form>
        <div class="mb-3">
          <label for="createTitle" class="form-label">Название</label>
          <input type="text" class="form-control" id="createTitle" aria-describedby="createTitle"
            placeholder="Введите название задачи" v-model="newTask.title">
          <div v-if="errors && errors.title" class="alert alert-danger">
            <span v-for="rules in errors.title" :key="rules">{{ rules }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label for="createDescription" class="form-label">Описание</label>
          <input type="text" class="form-control" id="createDescription" aria-describedby="createDescription"
            placeholder="Введите описание задачи" v-model="newTask.description">
          <div v-if="errors && errors.description" class="alert alert-danger">
            <span v-for="rules in errors.description" :key="rules">{{ rules }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label for="createDate" class="form-label">Дата</label>
          <input type="date" class="form-control" id="createDate" aria-describedby="createDate"
            placeholder="Введите дату" v-model="newTask.date">
        </div>

        <div class="mb-3">
          <label for="createTime" class="form-label">Время</label>
          <input type="time" class="form-control" id="createTime" aria-describedby="createTime"
            placeholder="Введите Время" v-model="newTask.time">
        </div>
      </form>
    </MDBModalBody>
    <MDBModalFooter>
      <MDBBtn color="secondary" @click="closeModal('create')">Закрыть</MDBBtn>
      <MDBBtn color="success" @click="save('create')">Создать</MDBBtn>
    </MDBModalFooter>
  </MDBModal>

  <MDBModal id="updateModal" tabindex="-1" labelledby="updateModalLabel" v-model="updateModal">
    <MDBModalHeader>
      <MDBModalTitle id="updateModalLabel"> Редактирование данных </MDBModalTitle>
    </MDBModalHeader>
    <MDBModalBody>

      <div v-if="errors" class="alert" style="color:red">
        <b> Проверьте корректность введённых данных: </b>
        <i class="fa-solid fa-arrow-turn-down" style="color: #ed1212;"></i>
      </div>

      <form>
        <div class="mb-3">
          <label for="updateTitle" class="form-label">Название</label>
          <input type="text" class="form-control" id="updateTitle" aria-describedby="updateTitle"
            placeholder="Редактировать название задачи" v-model="currentTask.title">
          <div v-if="errors && errors.title" class="alert alert-danger">
            <span v-for="rules in errors.title" :key="rules">{{ rules }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label for="updateDescription" class="form-label">Описание</label>
          <input type="text" class="form-control" id="updateDescription" aria-describedby="updateDescription"
            placeholder="Редактировать описание задачи" v-model="currentTask.description">
          <div v-if="errors && errors.description" class="alert alert-danger">
            <span v-for="rules in errors.description" :key="rules">{{ rules }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label for="updateDate" class="form-label">Дата</label>
          <input type="date" class="form-control" id="updateDate" aria-describedby="updateDate"
            placeholder="Редактировать дату" v-model="currentTask.date">
        </div>

        <div class="mb-3">
          <label for="updateTime" class="form-label">Время</label>
          <input type="time" class="form-control" id="updateTime" aria-describedby="updateTime"
            placeholder="Редактировать время" v-model="currentTask.time">
        </div>
      </form>
    </MDBModalBody>
    <MDBModalFooter>
      <MDBBtn color="secondary" @click="closeModal('update')">Закрыть</MDBBtn>
      <MDBBtn color="warning" @click="save('update')">Сохранить изменения</MDBBtn>
    </MDBModalFooter>
  </MDBModal>

  <MDBModal id="destroyModal" tabindex="-1" labelledby="destroyModalLabel" v-model="destroyModal">
    <MDBModalHeader>
      <MDBModalTitle>
        <div class="mb-4 text-danger">
          Вы действительно хотите удалить задачу?
        </div>
      </MDBModalTitle>
    </MDBModalHeader>
    <MDBModalBody>
      <form>
        <div class="mb-4 alert alert-warning">
          <strong>{{ currentTask.title }}</strong>
        </div>
      </form>
    </MDBModalBody>
    <MDBModalFooter>
      <MDBBtn class="btn-cancel" style="color: gray" @click="closeModal('destroy')">Отменить</MDBBtn>
      <MDBBtn class="btn-delete" style="color: red" @click="save('destroy')">Удалить</MDBBtn>
    </MDBModalFooter>
  </MDBModal>

</template>

<script>
import axios from 'axios';
import {
  MDBModal,
  MDBModalHeader,
  MDBModalTitle,
  MDBModalBody,
  MDBModalFooter,
  MDBBtn,
} from 'mdb-vue-ui-kit';

export default {
  components: {
    MDBModal,
    MDBModalHeader,
    MDBModalTitle,
    MDBModalBody,
    MDBModalFooter,
    MDBBtn,
  },

  data() {
    return {
      tasks: [],
      createModal: false,
      updateModal: false,
      destroyModal: false,
      errors: null,
      currentTask: null,
      newTask: {
        title: null,
        description: null,
        date: null,
        time: null
      }
    }
  },

  mounted() {
    let vue = this;
    axios.get("/api/tasks")
      .then(function (response) {
        vue.tasks = response.data.tasks;
      })
      .catch(function (error) {
        console.error(error);
      });
  },

  methods: {
    statusTask(task_status) {
      if (task_status === 0) return 'В работе';
      if (task_status === 1) return 'Выполнена';
    },

    showModal: function (modal, task = null) {
      if (modal === 'create') {
        this.createModal = true;
        this.errors = null;
        this.newTask = {
          title: null,
          description: null,
          date: null,
          time: null
        };
      } else if (modal === 'update') {
        if (task !== null) {
          this.currentTask = task;
        }
        this.updateModal = true;
      } else if (modal === 'destroy') {
        if (task !== null) {
          this.currentTask = task;
        }
        this.destroyModal = true;
      }
    },

    closeModal: function (modal) {
      if (modal === 'create') {
        this.createModal = false;
      } else if (modal === 'update') {
        this.updateModal = false;
      } else if (modal === 'destroy') {
        this.destroyModal = false;
      }
    },

    completed: function (id) {
      let vue = this;
      axios.post("/api/tasks/" + id + "/completed")
        .then(function (response) {
          vue.tasks = response.data.tasks;
        })
        .catch(function (error) {
          console.error(error);
        });
    },

    atWork: function (id) {
      let vue = this;
      axios.post("/api/tasks/" + id + "/at-work")
        .then(function (response) {
          vue.tasks = response.data.tasks;
        })
        .catch(function (error) {
          console.error(error);
        });
    },

    save: function (modal) {
      let vue = this;
      if (modal === 'create') {
        axios.post("/api/tasks", this.newTask)
          .then(function (response) {
            vue.tasks = response.data.tasks;
            vue.closeModal('create');
            vue.newTask = {
              title: null,
              description: null,
              date: null,
              time: null
            }
            vue.errors = null;
          })
          .catch(function (error) {
            if (error.response && error.response.data.errors) {
              vue.errors = error.response.data.errors;
            }
          });
      }
      else if (modal === 'update') {
        axios.patch("/api/tasks/" + vue.currentTask.id, vue.currentTask)
          .then(function (response) {
            vue.tasks = response.data.tasks;
            vue.closeModal('update');
            vue.errors = null;
          })
          .catch(function (error) {
            if (error.response && error.response.data.errors) {
              vue.errors = error.response.data.errors;
            }
          });
      }
      else if (modal === 'destroy') {
        axios.delete("/api/tasks/" + vue.currentTask.id)
          .then(function (response) {
            vue.tasks = response.data.tasks;
            vue.closeModal('destroy');
          })
          .catch(function (error) {
            console.error(error);
          });
      }
    }
  }
}
</script>

<style>
.TaskManager {
  font-family: 'Times New Roman', Times, serif;
}

.btn-addTask {
  background-color: rgba(148, 149, 151, 0.9);
  border-radius: 30px;
  color: white;
  font-size: 13px;
  letter-spacing: 1px;
  padding: 7px;
  margin-right: 110px;
  border: none;
}

.btn-addTask:hover {
  background-color: #616669;
  border: none;
}
</style>