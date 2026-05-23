from locust import HttpUser, task, between

class CarrosUser(HttpUser):
    host = "http://localhost:8080"
    wait_time = between(1, 3)

    @task(3)
    def listar_carros(self):
        self.client.get("/productos")

    @task(2)
    def ver_carro(self):
        self.client.get("/producto/1")

class MotosUser(HttpUser):
    host = "http://localhost:8080"
    wait_time = between(1, 2)

    @task(3)
    def listar_motos(self):
        self.client.get("/motos")

    @task(2)
    def ver_moto(self):
        self.client.get("/moto/1")

    @task(1)
    def validar_vehiculo(self):
        self.client.post("/validar/vehiculo", json={
            "tipo": "carro",
            "marca": "Toyota",
            "modelo": "Corolla",
            "precio": 85000000
        })