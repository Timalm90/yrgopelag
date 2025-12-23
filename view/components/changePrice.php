<section>
    <h2>Change hotel prices</h2>
    <form action="/app/admin/changePrice.php" method="post">
        <div>
            <label for="item">Choose room or tier level of features</label>
            <input type="text" name="item" placeholder="Room or tier" required>
        </div>

        <div>
            <label for="price">New price:</label>
            <input type="number" name="price" placeholder="Enter new price" required>
        </div>

        <button type="submit">Change price</button>
    </form>
</section>