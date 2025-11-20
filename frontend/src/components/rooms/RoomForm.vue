<template>
  <div class="modal fade" :id="modalId" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEdit ? 'Edit Kamar' : 'Tambah Kamar Baru' }}</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div v-if="error" class="alert alert-danger alert-dismissible fade show">
              <div v-if="typeof error === 'string'">{{ error }}</div>
              <ul v-else class="mb-0">
                <li v-for="(err, key) in error" :key="key">{{ err[0] }}</li>
              </ul>
              <button type="button" class="btn-close" @click="error = ''"></button>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="room_number" class="form-label"
                  >Nomor Kamar <span class="text-danger">*</span></label
                >
                <input
                  type="text"
                  class="form-control"
                  id="room_number"
                  v-model="form.room_number"
                  placeholder="Contoh: A101"
                  required
                />
              </div>

              <div class="col-md-6 mb-3">
                <label for="floor" class="form-label"
                  >Lantai <span class="text-danger">*</span></label
                >
                <input
                  type="number"
                  class="form-control"
                  id="floor"
                  v-model.number="form.floor"
                  min="1"
                  placeholder="1"
                  required
                />
              </div>

              <div class="col-md-6 mb-3">
                <label for="room_type" class="form-label"
                  >Tipe Kamar <span class="text-danger">*</span></label
                >
                <select class="form-select" id="room_type" v-model="form.room_type" required>
                  <option value="">Pilih Tipe</option>
                  <option value="single">Single (1 Orang)</option>
                  <option value="double">Double (2 Orang)</option>
                  <option value="shared">Shared (3+ Orang)</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="price" class="form-label"
                  >Harga/Bulan <span class="text-danger">*</span></label
                >
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input
                    type="number"
                    class="form-control"
                    id="price"
                    v-model.number="form.price"
                    min="0"
                    placeholder="1500000"
                    required
                  />
                </div>
              </div>

              <div class="col-md-12 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" v-model="form.status">
                  <option value="available">Tersedia</option>
                  <option value="occupied">Terisi</option>
                  <option value="maintenance">Maintenance</option>
                </select>
              </div>

              <div class="col-md-12 mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea
                  class="form-control"
                  id="description"
                  v-model="form.description"
                  rows="3"
                  placeholder="Deskripsi kamar..."
                ></textarea>
              </div>

              <div class="col-md-12 mb-3">
                <label class="form-label">Fasilitas</label>
                <div class="row">
                  <div
                    class="col-md-4"
                    v-for="(facility, index) in availableFacilities"
                    :key="index"
                  >
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :id="'facility-' + index"
                        :value="facility"
                        v-model="form.facilities"
                      />
                      <label class="form-check-label" :for="'facility-' + index">
                        {{ facility }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api'
import { Modal } from 'bootstrap'

export default {
  name: 'RoomForm',
  props: {
    modalId: {
      type: String,
      default: 'roomModal',
    },
    roomData: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      form: {
        room_number: '',
        floor: 1,
        room_type: '',
        price: '',
        status: 'available',
        description: '',
        facilities: [],
      },
      availableFacilities: [
        'AC',
        'Kasur',
        'Lemari',
        'Meja Belajar',
        'Kursi',
        'Kamar Mandi Dalam',
        'WiFi',
        'TV',
        'Kulkas',
      ],
      loading: false,
      error: '',
      modalInstance: null,
    }
  },
  computed: {
    isEdit() {
      return this.roomData !== null
    },
  },
  watch: {
    roomData: {
      handler(newVal) {
        if (newVal) {
          this.form = {
            room_number: newVal.room_number,
            floor: newVal.floor,
            room_type: newVal.room_type,
            price: newVal.price,
            status: newVal.status,
            description: newVal.description || '',
            facilities: newVal.facilities || [],
          }
        } else {
          this.resetForm()
        }
      },
      immediate: true,
    },
  },
  mounted() {
    const modalElement = document.getElementById(this.modalId)
    if (modalElement) {
      this.modalInstance = new Modal(modalElement)
    }
  },
  methods: {
    async handleSubmit() {
      this.loading = true
      this.error = ''

      try {
        let response

        if (this.isEdit) {
          response = await api.updateRoom(this.roomData.id, this.form)
        } else {
          response = await api.createRoom(this.form)
        }

        if (response.data.success) {
          this.$emit('success', response.data.data)
          this.closeModal()
          this.resetForm()
        }
      } catch (error) {
        if (error.response) {
          this.error = error.response.data.errors || error.response.data.message
        } else {
          this.error = 'Terjadi kesalahan. Silakan coba lagi.'
        }
      } finally {
        this.loading = false
      }
    },
    closeModal() {
      if (this.modalInstance) {
        this.modalInstance.hide()
      }
    },
    resetForm() {
      this.form = {
        room_number: '',
        floor: 1,
        room_type: '',
        price: '',
        status: 'available',
        description: '',
        facilities: [],
      }
      this.error = ''
    },
  },
}
</script>

<style scoped>
.form-label {
  font-weight: 500;
}
</style>
