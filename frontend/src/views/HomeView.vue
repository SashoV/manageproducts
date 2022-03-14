<script>
import Footer from "@/components/Footer.vue";
export default {
  data() {
    return {
      count: 0,
      products: [],
      checkedProducts: [],
    };
  },
  methods: {
    getProducts() {
      fetch(`${import.meta.env.VITE_APP_BACKEND_API_URL}/products`)
        .then((response) => response.json())
        .then((products) => {
          this.products = products;
        });
    },
    deleteSelected(event) {
      if (this.checkedProducts && this.checkedProducts.length) {
        var idsForDelete = [];
        this.checkedProducts.forEach((element) => {
          idsForDelete.push(element.id);
        });
        const requestOptions = {
          method: "POST",
          mode: "cors",
          cache: "default",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify(idsForDelete),
        };
        fetch(
          `${import.meta.env.VITE_APP_BACKEND_API_URL}/deleteProducts`,
          requestOptions
        )
          .then((response) => response.json())
          .then(() => {
            this.getProducts()
          })
          .then((idsForDelete = []));
      }
      // idsForDelete = [];
    },
  },
  mounted() {
    this.getProducts();
  },
  components: {
    Footer,
  },
};
</script>

<template>
  <div class="container-fluid">
    <div class="main">
      <div
        class="
          header
          d-flex
          justify-content-between
          align-items-end
          px-2
          pb-3
          pt-5
          border-bottom border-2
        "
      >
        <h3 class="mb-0">Product List</h3>
        <div class="d-block">
          <router-link to="/add-product" class="btn btn-primary btn-sm m-1"
            >ADD</router-link
          >
          <button class="btn btn-danger btn-sm m-1" @click="deleteSelected">
            MASS DELETE
          </button>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-3 mt-4" v-for="product in products" :key="product">
          <div class="card h-100">
            <div class="card-body text-center">
              <div class="form-check">
                <input
                  class="form-check-input delete-checkbox"
                  type="checkbox"
                  :value="product"
                  v-model="checkedProducts"
                />
              </div>
              <p class="card-text">{{ product.sku }}</p>
              <p class="card-text">{{ product.name }}</p>
              <p class="card-text">{{ product.price }} $</p>
              <p class="card-text">{{ product.attributeString }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="position-relative">
      <Footer />
    </div>
  </div>
</template>
